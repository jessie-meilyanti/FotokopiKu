@if($orders->isEmpty())
    <div class="p-6 text-center text-gray-600">Belum ada pesanan.</div>
@else
    @foreach($orders as $order)
        <div class="p-4 space-y-3 hover:bg-gray-50 dark:hover:bg-gray-900/60 transition" data-order-id="{{ $order->id }}">
            <div class="flex justify-between items-start">
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

            @if($order->tracks->isNotEmpty())
                <div class="pt-2 text-sm text-gray-600">
                    <strong>Tracking:</strong>
                    <ul class="mt-2 space-y-1">
                        @foreach($order->tracks as $t)
                            <li class="text-xs text-gray-500">{{ $t->created_at->format('d M H:i') }} — <span class="font-medium">{{ $t->status }}</span> {{ $t->location ? ' @ '.$t->location : '' }} {{ $t->note ? ' — '.$t->note : '' }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endforeach
@endif
{{-- removed duplicate rendering block to prevent duplicated orders list --}}
