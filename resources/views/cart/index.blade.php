<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Keranjang</h1>
            <a href="{{ route('home') }}" class="text-sm text-indigo-600">Tambah belanja</a>
        </div>

        @if ($cart->items->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center text-gray-600">
                Keranjang kosong.
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100/70 dark:border-gray-700 overflow-hidden">
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($cart->items as $item)
                        <div class="p-4 flex items-center justify-between">
                            <div>
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $item->product->name }}</div>
                                <div class="text-sm text-gray-500">Qty: {{ $item->qty }}</div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="font-semibold text-indigo-700 dark:text-indigo-300">
                                    Rp {{ number_format($item->qty * $item->product->price, 0, ',', '.') }}
                                </div>
                                                <form action="{{ route('cart.remove', $item) }}" method="POST">
                                                    @csrf
                                                    <x-button type="submit" color="red">Hapus</x-button>
                                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            @php
                $total = $cart->items->sum(fn($i) => $i->qty * $i->product->price);
            @endphp
            <div class="flex items-center justify-between bg-white dark:bg-gray-800 rounded-xl border border-gray-100/70 dark:border-gray-700 p-4">
                <div>
                    <div class="text-sm text-gray-500">Total</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($total, 0, ',', '.') }}</div>
                </div>
                <x-button href="{{ route('checkout.form') }}" color="indigo">Checkout</x-button>
            </div>
        @endif
    </div>
</x-app-layout>

