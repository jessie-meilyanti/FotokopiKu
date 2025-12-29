<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pesanan Saya</h1>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100/70 dark:border-gray-700">
            @forelse ($orders as $order)
                <a href="{{ route('orders.show', $order) }}" class="block px-3 sm:px-4 py-3 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900/60">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-gray-900 dark:text-white truncate">#{{ $order->id }} • {{ $order->tracking_code }}</div>
                            <div class="text-sm text-gray-500">{{ $order->created_at->format('d M Y H:i') }}</div>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 sm:flex-shrink-0">
                            <div class="font-semibold text-indigo-700 dark:text-indigo-300">Rp {{ number_format($order->total, 0, ',', '.') }}</div>
                            <div class="px-3 py-1 rounded-full text-sm {{ $order->status_badge_class }} w-fit">{{ $order->status_label }}</div>
                            <div class="flex items-center gap-2">
                                @if(!in_array($order->status, ['completed','cancelled']))
                                    <form action="{{ route('orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Batalkan pesanan ini?');" class="w-full sm:w-auto">
                                        @csrf
                                        <x-button type="submit" color="red" class="w-full sm:w-auto text-xs">Batal</x-button>
                                    </form>
                                @endif
                                <form action="{{ route('orders.reorder', $order) }}" method="POST" class="w-full sm:w-auto">
                                    @csrf
                                    <x-button type="submit" color="gray" class="w-full sm:w-auto text-xs">Pesan Lagi</x-button>
                                </form>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="p-6 text-center text-gray-600">Belum ada pesanan.</div>
            @endforelse
        </div>

        <div>
            {{ $orders->links() }}
        </div>
    </div>
</x-app-layout>

