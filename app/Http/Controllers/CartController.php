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

    public function remove(CartItem $item)
    {
        abort_unless($item->cart->user_id === auth()->id(), 403);
        $item->delete();

        return back()->with('success', 'Item dihapus');
    }
}

