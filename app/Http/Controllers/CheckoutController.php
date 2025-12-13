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
            'address' => 'required|string|min:10',
            'recipient' => 'required|string',
            'phone' => 'required|string',
            'payment_method' => 'required|string',
            'payment_bank' => 'nullable|string|max:150',
        ]);

        $subtotal = $cart->items->sum(fn ($i) => $i->qty * $i->product->price);
        $shipping = 0;

        $order = Order::create([
            'user_id' => auth()->id(),
            'recipient_name' => $data['recipient'],
            'recipient_phone' => $data['phone'],
            'shipping_address' => $data['address'],
            'status' => 'processing',
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $subtotal + $shipping,
            'payment_method' => $data['payment_method'],
            'payment_status' => 'unpaid',
            'payment_bank' => $data['payment_bank'] ?? null,
            'tracking_code' => 'TRK-' . Str::upper(Str::random(8)),
        ]);

        foreach ($cart->items as $ci) {
            $order->items()->create([
                'product_id' => $ci->product_id,
                'qty' => $ci->qty,
                'price' => $ci->product->price,
                'line_total' => $ci->qty * $ci->product->price,
            ]);
        }

        $cart->items()->delete();

        Address::firstOrCreate(
            ['user_id' => auth()->id(), 'label' => 'Utama'],
            [
                'recipient' => $data['recipient'],
                'phone' => $data['phone'],
                'full_address' => $data['address'],
            ]
        );

        OrderTrack::create([
            'order_id' => $order->id,
            'status' => 'Order dibuat',
            'location' => 'Toko',
            'note' => 'Menunggu proses',
        ]);

        return redirect()->route('orders.show', $order)->with('success', 'Order dibuat, lanjutkan pembayaran COD di toko.');
    }
}

