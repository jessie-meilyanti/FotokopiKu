<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white truncate">📦 Rincian Pesanan #{{ $order->id }}</h1>
                <div class="text-sm text-gray-500">Tracking: {{ $order->tracking_code ?? '-' }}</div>
            </div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3 sm:flex-shrink-0">
                <a href="{{ route('admin.orders.invoice', $order) }}" class="w-full sm:w-auto">
                    <x-button color="indigo" class="w-full sm:w-auto">📄 Invoice PDF</x-button>
                </a>
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-indigo-600">← Kembali ke Daftar</a>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-3 sm:gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100/70 dark:border-gray-700 p-3 sm:p-4 space-y-2">
                <div class="font-semibold text-gray-900 dark:text-white mb-3">Status Pesanan</div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Status</span>
                    <span class="font-semibold {{ $order->status_badge_class }} px-3 py-1 rounded-full">{{ $order->status_label }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Pembayaran</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ ucfirst($order->payment_status) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Metode</span>
                    <span class="text-gray-900 dark:text-white">{{ $order->payment_method }}</span>
                </div>
                @if($order->payment_method === 'Transfer')
                    <div class="text-sm text-gray-500">
                        Rekening: {{ $order->payment_bank ?? 'Hubungi kasir' }}<br>
                        Ref: {{ $order->payment_reference ?? '-' }}
                    </div>
                @endif
                <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                    <div class="text-sm text-gray-600">
                        <span class="font-medium">Pelanggan:</span> {{ $order->user?->name ?? 'Guest' }}<br>
                        <span class="font-medium">Email:</span> {{ $order->user?->email ?? '-' }}
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100/70 dark:border-gray-700 p-3 sm:p-4 space-y-3">
                <div class="font-semibold text-gray-900 dark:text-white">Pengiriman</div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">Metode</span>
                    @if($order->shipping_city)
                        <span class="px-2 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs">🚚 Kirim-antar</span>
                    @else
                        <span class="px-2 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs">🏪 Ambil sendiri</span>
                    @endif
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Penerima</span>
                    <span class="text-gray-900 dark:text-white font-medium">{{ $order->recipient_name ?? '-' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Telepon</span>
                    <span class="text-gray-900 dark:text-white font-medium">
                        @if($order->recipient_whatsapp)
                            {{ $order->recipient_whatsapp }}
                        @elseif($order->recipient_phone)
                            {{ $order->recipient_phone }}
                        @else
                            -
                        @endif
                    </span>
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-300">
                    @if($order->shipping_city)
                        Kirim ke: {{ $order->shipping_address ?? '-' }} ({{ $order->shipping_city }})
                    @else
                        Ambil di toko / kasir
                    @endif
                </div>
                <div class="border-t border-gray-100 dark:border-gray-700 pt-2 space-y-1">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-semibold text-gray-900 dark:text-white">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Ongkir</span>
                        <span class="text-gray-900 dark:text-white">Rp {{ number_format($order->shipping, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total</span>
                        <span class="text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100/70 dark:border-gray-700 p-4">
            <h2 class="font-semibold text-gray-900 dark:text-white mb-3">📋 Item Pesanan</h2>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach ($order->items as $item)
                    @php
                        $thumb = $item->product->thumbnail ? asset($item->product->thumbnail) : 'https://via.placeholder.com/80x80?text=Produk';
                    @endphp
                    <div class="py-3 flex items-center gap-3">
                        <img src="{{ $thumb }}" alt="{{ $item->product->name }}" class="w-16 h-16 rounded-lg object-cover border border-gray-100 dark:border-gray-700">
                        <div class="flex-1">
                            <div class="font-medium text-gray-900 dark:text-white">{{ $item->product->name }}</div>
                            <div class="text-sm text-gray-500">{{ $item->qty }} x Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-gray-900 dark:text-white font-semibold">Rp {{ number_format($item->line_total, 0, ',', '.') }}</div>
                            <div class="text-xs text-gray-500">Subtotal</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        @if($order->payment_proof_path)
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100/70 dark:border-gray-700 p-4">
                <h2 class="font-semibold text-gray-900 dark:text-white mb-3">💳 Bukti Pembayaran</h2>
                <a href="{{ asset('storage/' . $order->payment_proof_path) }}" target="_blank" class="block">
                    <img src="{{ asset('storage/' . $order->payment_proof_path) }}" alt="Bukti Pembayaran" class="max-w-md h-auto rounded-lg border border-gray-200 dark:border-gray-700 hover:opacity-90 transition">
                </a>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100/70 dark:border-gray-700 p-4">
            <h2 class="font-semibold text-gray-900 dark:text-white mb-3">⏱️ Progress Tracking</h2>
            @php
                $iconMap = [
                    'pending' => '⏳',
                    'processing' => '⚙️',
                    'shipped' => '🚚',
                    'completed' => '✅',
                    'cancelled' => '❌',
                ];
                $colorMap = [
                    'pending' => 'bg-yellow-500',
                    'processing' => 'bg-blue-500',
                    'shipped' => 'bg-indigo-500',
                    'completed' => 'bg-green-600',
                    'cancelled' => 'bg-red-600',
                ];
            @endphp
            <div class="relative">
                <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700"></div>
                <div class="space-y-4">
                    @forelse($order->tracks as $track)
                        @php
                            $status = strtolower($track->status);
                            $dotColor = $colorMap[$status] ?? 'bg-gray-400';
                            $icon = $iconMap[$status] ?? '📦';
                        @endphp
                        <div class="grid grid-cols-[48px_1fr] gap-3 items-start">
                            <div class="relative flex items-center justify-center">
                                <div class="w-4 h-4 rounded-full border-2 border-white dark:border-gray-800 {{ $dotColor }} shadow"></div>
                            </div>
                            <div class="p-3 rounded-lg border border-gray-100 dark:border-gray-700 hover:shadow-sm transition">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xl">{{ $icon }}</span>
                                        <div class="font-semibold text-gray-900 dark:text-white capitalize">{{ $track->status }}</div>
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $track->created_at->format('d M H:i') }}</div>
                                </div>
                                <div class="text-sm text-gray-500">{{ $track->location ?? '-' }}</div>
                                @if($track->note)
                                    <div class="text-sm text-gray-600 dark:text-gray-300">{{ $track->note }}</div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-gray-500 text-sm">Belum ada update tracking.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100/70 dark:border-gray-700 p-4">
            <h2 class="font-semibold text-gray-900 dark:text-white mb-3">🔧 Update Status</h2>
            <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="grid sm:grid-cols-4 gap-3">
                @csrf @method('PATCH')
                <select name="status" class="rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                    <option value="pending" @selected($order->status === 'pending')>Menunggu</option>
                    <option value="processing" @selected($order->status === 'processing')>Sedang Diproses</option>
                    <option value="completed" @selected($order->status === 'completed')>Selesai</option>
                    <option value="cancelled" @selected($order->status === 'cancelled')>Dibatalkan</option>
                </select>
                <select name="payment_status" class="rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                    <option value="unpaid" @selected($order->payment_status === 'unpaid')>Belum Bayar</option>
                    <option value="paid" @selected($order->payment_status === 'paid')>Lunas</option>
                    <option value="refunded" @selected($order->payment_status === 'refunded')>Dikembalikan</option>
                </select>
                <div class="sm:col-span-2 flex justify-end">
                    <x-button type="submit" color="indigo">💾 Update Status</x-button>
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100/70 dark:border-gray-700 p-4">
            <h2 class="font-semibold text-gray-900 dark:text-white mb-3">➕ Tambah Tracking</h2>
            <form action="{{ route('admin.orders.track', $order) }}" method="POST" class="grid sm:grid-cols-3 gap-3">
                @csrf
                <input type="text" name="status" placeholder="Status (mis. dikirim, sampai tujuan)" required class="rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                <input type="text" name="location" placeholder="Lokasi (mis. Gudang Jakarta)" class="rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                <div class="sm:col-span-1 flex items-center space-x-2">
                    <input type="text" name="note" placeholder="Catatan" class="flex-1 rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                    <x-button type="submit" color="green">✔️ Tambah</x-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
