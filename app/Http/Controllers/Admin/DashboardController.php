<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Total pendapatan dari pesanan selesai
        $totalRevenue = Order::where('status', 'completed')->sum('total');

        // Total pesanan
        $totalOrders = Order::count();

        // Pesanan per status
        $ordersByStatus = Order::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        // Penjualan bulanan 12 bulan terakhir
        $monthlySales = Order::where('created_at', '>=', now()->subMonths(12))
            ->where('status', 'completed')
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Produk terlaris (berdasarkan qty)
        $topProducts = OrderItem::select('product_id', DB::raw('SUM(qty) as total_qty'))
            ->whereHas('order', fn($q) => $q->where('status', 'completed'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->with('product')
            ->get();

        // Perhitungan kota dari alamat pengiriman (string match)
        $orders = Order::select('shipping_address')->get();
        $cityNames = ['Bekasi', 'Jakarta', 'Depok', 'Bogor', 'Tangerang'];
        $cityCounts = [];
        foreach ($cityNames as $name) {
            $cityCounts[$name] = 0;
        }
        $cityCounts['Ambil Sendiri'] = 0;
        foreach ($orders as $order) {
            if (!$order->shipping_address) {
                $cityCounts['Ambil Sendiri']++;
                continue;
            }
            $found = false;
            foreach ($cityNames as $name) {
                if (stripos($order->shipping_address, $name) !== false) {
                    $cityCounts[$name]++;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                // treat as Ambil Sendiri or unknown city
            }
        }
        arsort($cityCounts);
        $topCities = collect($cityCounts)->map(function ($total, $city) {
            return (object)['shipping_city' => $city, 'total' => $total];
        })->values()->take(5);

        // Pesanan terbaru
        $recentOrders = Order::with('user')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'ordersByStatus',
            'monthlySales',
            'topProducts',
            'topCities',
            'recentOrders'
        ));
    }
}
