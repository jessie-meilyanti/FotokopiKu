<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color:#111; font-size:12px; }
        .header { display:flex; justify-content:space-between; margin-bottom:12px; }
        .box { border:1px solid #ddd; padding:8px; border-radius:6px; margin-bottom:10px; }
        table { width:100%; border-collapse:collapse; }
        th, td { padding:8px; border-bottom:1px solid #eee; text-align:left; }
        th { background:#f7f7f7; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h2 style="margin:0;">Invoice #{{ $order->id }}</h2>
            <div>Tracking: {{ $order->tracking_code }}</div>
            <div>Tanggal: {{ $order->created_at->format('d M Y H:i') }}</div>
        </div>
        <div>
            <strong>FotokopiKu</strong><br>
            Jasa print & ATK
        </div>
    </div>

    <div class="box">
        <strong>Kepada:</strong><br>
        {{ $order->recipient_name ?? $order->user->name }}<br>
        {{ $order->recipient_phone ?? $order->user->phone }}<br>
        {{ $order->shipping_address ?? '-' }}
    </div>

    <div class="box">
        <strong>Metode Bayar:</strong> {{ $order->payment_method }} {{ $order->payment_bank ? '('.$order->payment_bank.')' : '' }}<br>
        <strong>Status Bayar:</strong> {{ ucfirst($order->payment_status) }}<br>
        <strong>Status Pesanan:</strong> {{ ucfirst($order->status) }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>Rp {{ number_format($item->price,0,',','.') }}</td>
                    <td>Rp {{ number_format($item->line_total,0,',','.') }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3" style="text-align:right;"><strong>Subtotal</strong></td>
                <td>Rp {{ number_format($order->subtotal,0,',','.') }}</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align:right;"><strong>Ongkir</strong></td>
                <td>Rp {{ number_format($order->shipping,0,',','.') }}</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align:right;"><strong>Total</strong></td>
                <td><strong>Rp {{ number_format($order->total,0,',','.') }}</strong></td>
            </tr>
        </tbody>
    </table>
</body>
</html>

