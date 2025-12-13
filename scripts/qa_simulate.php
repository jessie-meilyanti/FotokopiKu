<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderTrack;
use Illuminate\Support\Str;

echo "Starting QA simulation...\n";

$user = User::where('email', 'user@fotokopi.test')->first();
if (! $user) {
    echo "NO_USER\n";
    exit(1);
}

$products = Product::inRandomOrder()->take(2)->get();
if ($products->isEmpty()) {
    echo "NO_PRODUCTS\n";
    exit(1);
}

$cart = Cart::firstOrCreate(['user_id' => $user->id]);
foreach ($products as $p) {
    $item = $cart->items()->firstOrCreate(['product_id' => $p->id], ['qty' => 0]);
    $item->increment('qty', 1);
}

$subtotal = $cart->items->sum(function ($i) {
    return $i->qty * $i->product->price;
});

$order = Order::create([
    'user_id' => $user->id,
    'recipient_name' => $user->name,
    'recipient_phone' => '0812345678',
    'shipping_address' => 'Jl. Tester 1',
    'status' => 'pending',
    'subtotal' => $subtotal,
    'shipping' => 5000,
    'total' => $subtotal + 5000,
    'payment_method' => 'Transfer',
    'payment_status' => 'unpaid',
    'payment_bank' => 'BCA',
    'tracking_code' => 'TRK-'.Str::upper(Str::random(8)),
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

OrderTrack::create([
    'order_id' => $order->id,
    'status' => 'Order dibuat',
    'location' => 'Toko',
    'note' => 'Simulasi script',
]);

echo "CREATED_ORDER: id={$order->id} status={$order->status_label} badge={$order->status_badge_class} total={$order->total}\n";

$order->update(['status' => 'processing']);
OrderTrack::create([
    'order_id' => $order->id,
    'status' => 'Sedang diproses',
    'location' => 'Gudang',
    'note' => 'Simulasi update',
]);

echo "UPDATED: id={$order->id} status={$order->status_label}\n";

// create another order then cancel it
$order2 = Order::create([
    'user_id' => $user->id,
    'recipient_name' => $user->name,
    'recipient_phone' => '0812345678',
    'shipping_address' => 'Jl. Tester 2',
    'status' => 'pending',
    'subtotal' => 1000,
    'shipping' => 0,
    'total' => 1000,
    'payment_method' => 'COD',
    'payment_status' => 'unpaid',
    'payment_bank' => null,
    'tracking_code' => 'TRK-'.Str::upper(Str::random(8)),
]);
OrderTrack::create([
    'order_id' => $order2->id,
    'status' => 'Order dibuat',
    'location' => 'Toko',
    'note' => 'Simulasi cancel',
]);
$order2->update(['status' => 'cancelled']);
OrderTrack::create([
    'order_id' => $order2->id,
    'status' => 'Dibatalkan',
    'location' => 'User',
    'note' => 'Dibatalkan melalui script',
]);

echo "CANCELLED_ORDER: id={$order2->id} status={$order2->status_label}\n";

// test reorder: add items from first order to cart
$cart2 = Cart::firstOrCreate(['user_id' => $user->id]);
foreach ($order->items as $oi) {
    $ci = $cart2->items()->firstOrCreate(['product_id' => $oi->product_id], ['qty' => 0]);
    $ci->increment('qty', $oi->qty);
}

echo "REORDER: cart_items_count=" . $cart2->items()->count() . "\n";

echo "QA simulation completed.\n";
