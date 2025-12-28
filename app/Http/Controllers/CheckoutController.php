<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderTrack;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function form()
    {
        $cart = Cart::with('items.product')->where('user_id', auth()->id())->firstOrFail();

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang masih kosong');
        }

        $addresses = Address::where('user_id', auth()->id())->get();

        return view('checkout.form', compact('cart', 'addresses'));
    }

    public function process(Request $request)
    {
        $cart = Cart::with('items.product')->where('user_id', auth()->id())->firstOrFail();

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang masih kosong');
        }

        $data = $request->validate([
            'address' => 'required_if:delivery_method,kirim-antar|nullable|string|min:10',
            'recipient' => 'required|string',
            'whatsapp' => 'required|string',
            'delivery_method' => 'required|string|in:ambil-di-tempat,kirim-antar',
            'city' => 'required_if:delivery_method,kirim-antar|nullable|string',
            'payment_method' => 'required|string|in:COD,QRIS',
            'payment_proof' => 'required_if:payment_method,QRIS|nullable|image|max:2048',
        ]);

        $subtotal = $cart->items->sum(fn ($i) => $i->qty * $i->product->price);
        $totalQty = $cart->items->sum(fn($i) => $i->qty);
        $shipping = 0;

        // Jika kirim-antar, cek minimal 10 item
        if ($data['delivery_method'] === 'kirim-antar') {
            if ($totalQty < 10) {
                return back()->withInput()->with('error', 'Kirim-antar minimal 10 item. Total Anda: ' . $totalQty);
            }

            // Hitung ongkir berdasarkan kota
            $city = strtolower($data['city'] ?? '');
            if (str_contains($city, 'jakarta')) {
                $shipping = 15000;
            } elseif (str_contains($city, 'bogor')) {
                $shipping = 20000;
            } elseif (str_contains($city, 'depok')) {
                $shipping = 18000;
            } elseif (str_contains($city, 'tangerang')) {
                $shipping = 22000;
            } elseif (str_contains($city, 'bekasi')) {
                $shipping = 12000; // toko di bekasi, lebih murah
            } else {
                $shipping = 25000; // luar jabodetabek
            }
        }

        $order = Order::create([
            'user_id' => auth()->id(),
            'recipient_name' => $data['recipient'],
            'recipient_phone' => null,
            'recipient_whatsapp' => $data['whatsapp'],
            'shipping_address' => $data['address'],
            'shipping_city' => $data['city'] ?? null,
            'status' => 'processing',
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $subtotal + $shipping,
            'payment_method' => $data['payment_method'],
            'payment_status' => 'unpaid',
            'payment_bank' => null,
            'tracking_code' => 'TRK-' . Str::upper(Str::random(8)),
        ]);

        // Upload bukti bayar jika ada
        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('payment_proofs', 'public');
            $order->update(['payment_proof_path' => $path]);
        }

        foreach ($cart->items as $ci) {
            $order->items()->create([
                'product_id' => $ci->product_id,
                'qty' => $ci->qty,
                'price' => $ci->product->price,
                'line_total' => $ci->qty * $ci->product->price,
            ]);
        }

        $cart->items()->delete();

        if ($data['delivery_method'] === 'kirim-antar') {
            Address::firstOrCreate(
                ['user_id' => auth()->id(), 'label' => 'Utama'],
                [
                    'recipient' => $data['recipient'],
                    'phone' => $data['whatsapp'],
                    'full_address' => $data['address'],
                ]
            );
        }

        OrderTrack::create([
            'order_id' => $order->id,
            'status' => 'Order dibuat',
            'location' => $data['delivery_method'] === 'ambil-di-tempat' ? 'Toko - Ruko Summarecon Bekasi' : 'Persiapan pengiriman',
            'note' => $data['delivery_method'] === 'ambil-di-tempat' ? 'Silakan ambil di toko kami' : 'Pesanan akan dikirim',
        ]);

        $message = $data['delivery_method'] === 'ambil-di-tempat' 
            ? 'Order dibuat! Silakan bayar & ambil di Ruko Summarecon Bekasi.' 
            : 'Order dibuat! Kami akan kirim pesanan Anda.';

        return redirect()->route('orders.index')->with('success', $message);
    }
}

