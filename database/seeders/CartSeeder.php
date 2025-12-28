<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();
        if ($products->isEmpty()) {
            $this->command->warn('No products found for carts. Seed products first.');
            return;
        }

        // Seed carts for all non-admin users
        $users = User::where('is_admin', false)->get();
        foreach ($users as $user) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
            // Add 1-3 random items
            foreach ($products->random(min(3, max(1, $products->count()))) as $product) {
                $qty = rand(1, 5);
                $existing = $cart->items()->where('product_id', $product->id)->first();
                if ($existing) {
                    $existing->update(['qty' => $qty]);
                } else {
                    $cart->items()->create([
                        'product_id' => $product->id,
                        'qty' => $qty,
                    ]);
                }
            }
        }

        $this->command->info('✅ Berhasil membuat keranjang dummy untuk semua user.');
    }
}
