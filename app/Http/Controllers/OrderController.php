<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderTrack;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())->latest()->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_unless($order->user_id === auth()->id() || auth()->user()->is_admin, 403);
        $order->load('items.product', 'tracks');

        return view('orders.show', compact('order'));
    }

    public function uploadProof(Request $request, Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $data = $request->validate([
            'payment_proof' => 'required|image|max:2048',
            'payment_reference' => 'nullable|string|max:255',
        ]);

        if ($order->payment_proof_path) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $order->payment_proof_path));
        }

        $path = $data['payment_proof']->store('orders/proof', 'public');

        $order->update([
            'payment_proof_path' => Storage::url($path),
            'payment_reference' => $data['payment_reference'] ?? $order->payment_reference,
            'payment_status' => 'paid',
        ]);

        OrderTrack::create([
            'order_id' => $order->id,
            'status' => 'Pembayaran dikirim',
            'location' => 'Online',
            'note' => 'Menunggu verifikasi',
        ]);

        return back()->with('success', 'Bukti pembayaran diunggah');
    }

    public function invoice(Order $order)
    {
        abort_unless($order->user_id === auth()->id() || auth()->user()->is_admin, 403);
        $order->load('items.product', 'user');

        $pdf = Pdf::loadView('pdf.invoice', ['order' => $order])->setPaper('a4');

        return $pdf->download("invoice-{$order->id}.pdf");
    }

    public function cancel(Request $request, Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        if (in_array($order->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Pesanan tidak dapat dibatalkan.');
        }

        $order->update(['status' => 'cancelled']);

        OrderTrack::create([
            'order_id' => $order->id,
            'status' => 'Dibatalkan',
            'location' => 'User',
            'note' => 'Dibatalkan oleh pelanggan',
        ]);

        return back()->with('success', 'Pesanan dibatalkan.');
    }

    public function reorder(Request $request, Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);

        foreach ($order->items as $item) {
            $cartItem = $cart->items()->firstOrCreate([
                'product_id' => $item->product_id
            ], ['qty' => 0]);

            $cartItem->increment('qty', $item->qty);
        }

        return redirect()->route('cart.index')->with('success', 'Item pesanan ditambahkan ke keranjang.');
    }
}

