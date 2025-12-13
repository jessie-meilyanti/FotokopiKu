<x-app-layout>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Produk</h1>
            <x-button href="{{ route('admin.products.create') }}" color="indigo">Tambah</x-button>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100/70 dark:border-gray-700">
            @foreach ($products as $product)
                <div class="p-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center space-x-3">
                        <img src="{{ $product->thumbnail ?? 'https://via.placeholder.com/80x80?text=Produk' }}" class="w-16 h-16 object-cover rounded-md">
                        <div>
                            <div class="font-semibold text-gray-900 dark:text-white">{{ $product->name }}</div>
                            <div class="text-sm text-gray-500">{{ $product->category?->name }} • Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('admin.products.edit', $product) }}" class="text-sm text-indigo-600">Edit</a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="text-sm text-red-600">Hapus</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{ $products->links() }}
    </div>
</x-app-layout>

