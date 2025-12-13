<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderTrack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items.product', 'user')->latest()->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    public function dashboard()
    {
        $end = Carbon::now()->startOfMonth();
        $start = (clone $end)->subMonths(11);

        $months = [];
        $data = [];

        for ($dt = $start; $dt <= $end; $dt->addMonth()) {
            $months[] = $dt->format('M Y');
            $m = $dt->format('Y-m');
            $total = Order::whereYear('created_at', $dt->year)
                ->whereMonth('created_at', $dt->month)
                ->sum('total');
            $data[] = (float) $total;
        }

        return view('admin.dashboard', ['months' => $months, 'data' => $data]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|string',
            'payment_status' => 'required|string',
        ]);

        $order->update($data);

        // create a tracking entry for status change
        OrderTrack::create([
            'order_id' => $order->id,
            'status' => 'Status diperbarui: ' . ($data['status'] ?? $order->status),
            'location' => 'Admin',
            'note' => 'Pembayaran: ' . ($data['payment_status'] ?? $order->payment_status),
        ]);

        return back()->with('success', 'Status pesanan diperbarui');
    }

    public function addTrack(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:500',
        ]);

        OrderTrack::create([
            'order_id' => $order->id,
            'status' => $data['status'],
            'location' => $data['location'] ?? null,
            'note' => $data['note'] ?? null,
        ]);

        return back()->with('success', 'Tracking ditambahkan');
    }

    public function invoice(Order $order)
    {
        $order->load('items.product', 'user');
        $pdf = Pdf::loadView('pdf.invoice', ['order' => $order])->setPaper('a4');

        return $pdf->download("invoice-{$order->id}.pdf");
    }
}

