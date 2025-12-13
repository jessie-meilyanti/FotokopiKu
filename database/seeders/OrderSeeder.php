<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderTrack;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'user@fotokopi.test')->first();
        if (! $user) return;

        $products = Product::inRandomOrder()->take(6)->get();

        // create 6 sample orders with mixed statuses
        $statuses = ['pending', 'processing', 'completed', 'cancelled'];

        foreach (range(1, 6) as $i) {
            $selected = $products->random( min(3, $products->count()) );
            $subtotal = $selected->sum(fn($p) => $p->price);

            $order = Order::create([
                'user_id' => $user->id,
                'recipient_name' => $user->name,
                'recipient_phone' => '0812345678',
                'shipping_address' => 'Jl. Contoh No. '.$i,
                'status' => $statuses[array_rand($statuses)],
                'subtotal' => $subtotal,
                'shipping' => 5000,
                'total' => $subtotal + 5000,
                'payment_method' => 'Transfer',
                'payment_status' => 'unpaid',
                'payment_bank' => 'BCA',
                'tracking_code' => 'TRK-'.Str::upper(Str::random(8)),
            ]);

            foreach ($selected as $p) {
                $order->items()->create([
                    'product_id' => $p->id,
                    'qty' => 1,
                    'price' => $p->price,
                    'line_total' => $p->price,
                ]);
            }

            // add some tracks
            OrderTrack::create([
                'order_id' => $order->id,
                'status' => 'Order dibuat',
                'location' => 'Toko',
                'note' => 'Seeder - order sample',
            ]);

            if ($order->status === 'processing') {
                OrderTrack::create([
                    'order_id' => $order->id,
                    'status' => 'Sedang diproses',
                    'location' => 'Gudang',
                    'note' => 'Dikerjakan oleh admin',
                ]);
            }
        }
    }
}
