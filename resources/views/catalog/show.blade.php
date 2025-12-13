<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100/70 dark:border-gray-700 overflow-hidden">
            <div class="grid md:grid-cols-2 gap-6">
                <img src="{{ $product->thumbnail ?? 'https://via.placeholder.com/800x600?text=Produk' }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                <div class="p-6 space-y-4">
                    <div class="text-sm text-gray-500 uppercase">{{ $product->category?->name }}</div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $product->name }}</h1>
                    <div class="text-lg text-gray-700 dark:text-gray-200 leading-relaxed">
                        {{ $product->description }}
                    </div>
                    <div class="text-2xl font-bold text-indigo-700 dark:text-indigo-300">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </div>
                    <div class="flex items-center space-x-3">
                        @auth
                            <form action="{{ route('cart.add', $product) }}" method="POST" class="flex items-center space-x-2">
                                @csrf
                                <input type="number" name="qty" min="1" value="1" class="w-20 rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                                <x-button type="submit" color="indigo">Tambah ke keranjang</x-button>
                            </form>
                        @else
                            <x-button href="{{ route('login') }}" color="indigo">Masuk untuk beli</x-button>
                        @endauth
                    </div>
                    <div class="text-sm text-gray-500">Stok: {{ $product->stock }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

