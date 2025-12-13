<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pesanan #{{ $order->id }}</h1>
                <div class="text-sm text-gray-500">Tracking: {{ $order->tracking_code ?? '-' }}</div>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('orders.invoice', $order) }}">
                    <x-button color="indigo">Unduh Invoice</x-button>
                </a>
                <a href="{{ route('orders.index') }}" class="text-sm text-indigo-600">Kembali</a>
                @if(!in_array($order->status, ['completed','cancelled']))
                    <form action="{{ route('orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Batalkan pesanan ini?');">
                        @csrf
                        <x-button type="submit" color="red">Batalkan Pesanan</x-button>
                    </form>
                @endif
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100/70 dark:border-gray-700 p-4 space-y-2">
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
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100/70 dark:border-gray-700 p-4 space-y-3">
                <div class="font-semibold text-gray-900 dark:text-white">Pengiriman</div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Penerima</span>
                    <span class="text-gray-900 dark:text-white">{{ $order->recipient_name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Telepon</span>
                    <span class="text-gray-900 dark:text-white">{{ $order->recipient_phone ?? '-' }}</span>
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-300">Alamat: {{ $order->shipping_address ?? '-' }}</div>
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
            <h2 class="font-semibold text-gray-900 dark:text-white mb-3">Item Pesanan</h2>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach ($order->items as $item)
                    <div class="py-3 flex justify-between">
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">{{ $item->product->name }}</div>
                            <div class="text-sm text-gray-500">Qty {{ $item->qty }}</div>
                        </div>
                        <div class="text-gray-900 dark:text-white font-semibold">
                            Rp {{ number_format($item->line_total, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        @if($order->payment_method === 'Transfer' && $order->payment_status !== 'paid')
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100/70 dark:border-gray-700 p-4 space-y-3">
                <h2 class="font-semibold text-gray-900 dark:text-white">Upload Bukti Transfer</h2>
                    <form action="{{ route('orders.proof', $order) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <input type="text" name="payment_reference" placeholder="Nomor referensi / catatan" class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                    <input type="file" name="payment_proof" required class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                    <x-button type="submit" color="indigo">Kirim Bukti</x-button>
                </form>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100/70 dark:border-gray-700 p-4 space-y-3">
            <h2 class="font-semibold text-gray-900 dark:text-white">Tracking Pengiriman</h2>
            <div class="space-y-2">
                @forelse($order->tracks as $track)
                    <div class="p-3 rounded-lg border border-gray-100 dark:border-gray-700 hover:shadow-sm transition">
                        <div class="flex justify-between">
                            <div class="font-semibold text-gray-900 dark:text-white">{{ $track->status }}</div>
                            <div class="text-xs text-gray-500">{{ $track->created_at->format('d M H:i') }}</div>
                        </div>
                        <div class="text-sm text-gray-500">{{ $track->location ?? '-' }}</div>
                        @if($track->note)
                            <div class="text-sm text-gray-600 dark:text-gray-300">{{ $track->note }}</div>
                        @endif
                    </div>
                @empty
                    <div class="text-gray-500 text-sm">Belum ada update tracking.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>

