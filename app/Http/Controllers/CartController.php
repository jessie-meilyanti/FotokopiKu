<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);
        $cart->load('items.product');

        return view('cart.index', [
            'cart' => $cart,
        ]);
    }

    public function add(Request $request, Product $product)
    {
        $request->validate([
            'qty' => 'sometimes|integer|min:1',
        ]);

        $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);
        $item = $cart->items()->firstOrCreate(['product_id' => $product->id], ['qty' => 0]);
        $item->increment('qty', $request->integer('qty', 1));

        return back()->with('success', 'Produk ditambahkan ke keranjang');
    }

    public function updateQuantity(Request $request, CartItem $item)
    {
        abort_unless($item->cart->user_id === auth()->id(), 403);

        $data = $request->validate([
            'action' => 'required|in:increment,decrement,set',
            'value' => 'sometimes|integer|min:1|max:999',
        ]);
        $cartId = $item->cart_id;

        if ($data['action'] === 'increment') {
            $item->increment('qty');
        } elseif ($data['action'] === 'decrement') {
            if ($item->qty <= 1) {
                $item->delete();
            } else {
                $item->decrement('qty');
            }
        } elseif ($data['action'] === 'set') {
            $newQty = $data['value'] ?? 1;
            if ($newQty < 1) {
                $item->delete();
            } else {
                $item->update(['qty' => $newQty]);
            }
        }

        $cart = Cart::with('items.product')->find($cartId);
        $subtotal = 0;
        $totalQty = 0;
        if ($cart) {
            foreach ($cart->items as $ci) {
                $subtotal += ($ci->qty * $ci->product->price);
                $totalQty += $ci->qty;
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'qty' => $item->exists ? $item->qty : 0,
                'removed' => !$item->exists || ($item->exists && $item->qty === 0),
                'subtotal' => $subtotal,
                'totalQty' => $totalQty,
                'status' => 'ok',
            ]);
        }

        return back()->with('success', $data['action'] === 'increment' ? 'Jumlah produk diperbarui' : ($item->exists ? 'Jumlah produk diperbarui' : 'Item dihapus'));
    }

    public function remove(CartItem $item)
    {
        abort_unless($item->cart->user_id === auth()->id(), 403);
        $item->delete();

        return back()->with('success', 'Item dihapus');
    }
}

