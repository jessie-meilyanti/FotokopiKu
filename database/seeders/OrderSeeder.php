<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderTrack;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Nama pelanggan sesuai permintaan
        $customerNames = [
            'Olivia', 'Eliana', 'Aurora', 'Amelia', 'Eleanor', 'Luna', 'Zairee', 'Selina', 'Quinlyn', 'Kayesha',
            'Naura', 'Callysta', 'Noah', 'Leo', 'Luca', 'Zayn', 'Arka', 'Elio', 'Kael', 'Ezra',
            'Ravi', 'Kenzo', 'Raka', 'Nara', 'Dio', 'Arya', 'Fino', 'Lio', 'Rafi', 'Druv', 'Lian',
        ];

        $cities = [
            ['name' => 'Bekasi', 'cost' => 12000],
            ['name' => 'Jakarta', 'cost' => 15000],
            ['name' => 'Depok', 'cost' => 18000],
            ['name' => 'Bogor', 'cost' => 20000],
            ['name' => 'Tangerang', 'cost' => 22000],
            ['name' => null, 'cost' => 0], // Ambil sendiri
        ];

        $paymentMethods = [
            ['method' => 'COD', 'bank' => null],
            ['method' => 'QRIS', 'bank' => null],
        ];

        // Pastikan tiap nama punya akun dengan email nama@fotokopi.com
        $users = [];
        foreach ($customerNames as $nm) {
            $email = strtolower($nm).'@fotokopi.com';
            $users[$nm] = User::firstOrCreate(
                ['email' => $email],
                ['name' => $nm, 'password' => bcrypt('password')]
            );
        }

        $products = Product::all();
        if ($products->isEmpty()) {
            $this->command->warn('No products found. Please seed products first.');
            return;
        }

        // Buat 12 pesanan (Jan-Des 2025) untuk setiap user bernama
        foreach ($users as $name => $user) {
            for ($month = 1; $month <= 12; $month++) {
                $orderDate = Carbon::create(2025, $month, rand(1, 28), rand(8, 20), rand(0, 59));

                $cityData = $cities[array_rand($cities)];
                $shippingCost = $cityData['cost'];
                $shippingCity = $cityData['name'];

                $selectedProducts = $products->random(rand(2, min(5, $products->count())));
                $subtotal = 0;
                $orderItems = [];
                foreach ($selectedProducts as $product) {
                    $qty = rand(1, 10);
                    $line = $product->price * $qty;
                    $subtotal += $line;
                    $orderItems[] = [
                        'product_id' => $product->id,
                        'qty' => $qty,
                        'price' => $product->price,
                        'line_total' => $line,
                    ];
                }

                $payment = $paymentMethods[array_rand($paymentMethods)];
                $status = collect(['pending','processing','completed'])->random();
                $paymentStatus = $status === 'completed' ? 'paid' : 'unpaid';

                $order = Order::create([
                    'user_id' => $user->id,
                    'recipient_name' => $name,
                    'recipient_phone' => '08'.rand(1000000000, 9999999999),
                    'shipping_address' => $shippingCity ? ('Jl. '.Str::random(10).' No. '.rand(1,99).', '.$shippingCity) : null,
                    'shipping_city' => $shippingCity,
                    'status' => $status,
                    'subtotal' => $subtotal,
                    'shipping' => $shippingCost,
                    'total' => $subtotal + $shippingCost,
                    'payment_method' => $payment['method'],
                    'payment_status' => $paymentStatus,
                    'payment_bank' => $payment['bank'],
                    'tracking_code' => 'TRK-'.Str::upper(Str::random(8)),
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);

                foreach ($orderItems as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'qty' => $item['qty'],
                        'price' => $item['price'],
                        'line_total' => $item['line_total'],
                        'created_at' => $orderDate,
                        'updated_at' => $orderDate,
                    ]);
                }

                // Timeline tracking
                OrderTrack::create([
                    'order_id' => $order->id,
                    'status' => 'pending',
                    'location' => $shippingCity,
                    'note' => 'Pesanan dibuat',
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);
                OrderTrack::create([
                    'order_id' => $order->id,
                    'status' => 'processing',
                    'location' => $shippingCity,
                    'note' => 'Pesanan diproses',
                    'created_at' => $orderDate->copy()->addHours(2),
                    'updated_at' => $orderDate->copy()->addHours(2),
                ]);
                if ($status === 'completed') {
                    OrderTrack::create([
                        'order_id' => $order->id,
                        'status' => 'completed',
                        'location' => $shippingCity,
                        'note' => 'Pesanan selesai',
                        'created_at' => $orderDate->copy()->addHours(rand(6, 48)),
                        'updated_at' => $orderDate->copy()->addHours(rand(6, 48)),
                    ]);
                }
            }
        }

        $this->command->info('✅ Berhasil membuat pesanan dummy Jan-Des 2025 untuk semua user bernama.');
    }
}
