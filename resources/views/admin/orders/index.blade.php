<x-app-layout>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pesanan</h1>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100/70 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-700">
            @foreach ($orders as $order)
                <div class="p-4 space-y-3 hover:bg-gray-50 dark:hover:bg-gray-900/60 transition">
                    <div class="flex justify-between">
                        <div>
                            <div class="font-semibold text-gray-900 dark:text-white">#{{ $order->id }} • {{ $order->user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $order->tracking_code }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-semibold text-indigo-700 dark:text-indigo-300">Rp {{ number_format($order->total, 0, ',', '.') }}</div>
                            <div class="text-sm text-gray-500">{{ $order->created_at->format('d M Y H:i') }}</div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                        <span>Status:</span>
                        <span class="font-medium {{ $order->status_badge_class }} px-2 py-1 rounded-full">{{ $order->status_label }}</span>
                        <span class="text-gray-400">•</span>
                        <span>Bayar: <span class="font-medium">{{ ucfirst($order->payment_status) }}</span></span>
                        <span class="text-gray-400">•</span>
                        <a href="{{ route('admin.orders.invoice', $order) }}" class="text-indigo-600 hover:underline">Invoice PDF</a>
                        <span class="text-gray-400">•</span>
                        <span class="text-sm text-gray-500">{{ $order->tracking_code ?? '-' }}</span>
                    </div>
                    <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="grid sm:grid-cols-4 gap-2">
                        @csrf @method('PATCH')
                        <select name="status" class="rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                            @foreach (['pending','processing','completed','cancelled'] as $st)
                                <option value="{{ $st }}" @selected($order->status === $st)>{{ ucfirst($st) }}</option>
                            @endforeach
                        </select>
                        <select name="payment_status" class="rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                            @foreach (['unpaid','paid','refunded'] as $ps)
                                <option value="{{ $ps }}" @selected($order->payment_status === $ps)>{{ ucfirst($ps) }}</option>
                            @endforeach
                        </select>
                        <div class="sm:col-span-2 flex justify-end">
                            <x-button type="submit" color="indigo">Update</x-button>
                        </div>
                    </form>
                    <form action="{{ route('admin.orders.track', $order) }}" method="POST" class="grid sm:grid-cols-3 gap-2">
                        @csrf
                        <input type="text" name="status" placeholder="Status baru (mis. dikirim)" class="rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                        <input type="text" name="location" placeholder="Lokasi (mis. Gudang/JNE)" class="rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                        <div class="sm:col-span-1 flex items-center space-x-2">
                            <input type="text" name="note" placeholder="Catatan" class="flex-1 rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                            <x-button type="submit" color="green">Tambah</x-button>
                        </div>
                    </form>
                </div>
            @endforeach
        </div>

        {{ $orders->links() }}
    </div>
</x-app-layout>

