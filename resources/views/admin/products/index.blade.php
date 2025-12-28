<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 py-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">🛍️ Produk & Jasa</h1>
        </div>

        <form method="GET" action="{{ route('admin.products.index') }}" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 flex flex-wrap items-center gap-3 shadow-sm">
            <label for="category" class="text-sm font-medium text-gray-700 dark:text-gray-300">🏷️ Kategori:</label>
            <select id="category" name="category" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">Semua Kategori</option>
                @isset($categories)
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(($categoryId ?? null) == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                @endisset
            </select>
            <button class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium transition shadow-sm">Terapkan</button>
            @if(($categoryId ?? null))
                <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">✖ Reset</a>
            @endif
        </form>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach ($products as $product)
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm hover:shadow-md transition group">
                    <div class="relative aspect-square">
                        <img src="{{ $product->thumbnail ?: '/images/product-placeholder.svg' }}" 
                             alt="{{ $product->name }}"
                             loading="lazy" 
                             class="w-full h-full object-cover">
                        @php($isPrintCategory = isset($product->category) && preg_match('/kertas|print/i', $product->category->name))
                        <div class="absolute top-2 right-2">
                            <span class="text-xs px-2.5 py-1 rounded-full bg-white/90 dark:bg-gray-900/90 text-gray-800 dark:text-gray-100 font-medium backdrop-blur-sm shadow-sm">
                                {{ ($isPrintCategory || !$product->is_service) ? '📦 Produk' : '🧰 Jasa' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-4 space-y-3">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white text-base line-clamp-2 mb-2">
                                {{ $product->name }}
                            </h3>
                            @if($product->description)
                                <p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-2 mb-3">
                                    {{ $product->description }}
                                </p>
                            @endif
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <span class="mr-2">🏷️</span>
                                <span class="truncate">{{ $product->category?->name ?? 'Tanpa Kategori' }}</span>
                            </div>
                            <div class="flex items-center text-base font-bold text-indigo-600 dark:text-indigo-400">
                                <span class="mr-2">💰</span>
                                <span>Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="flex gap-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                            <a href="{{ route('admin.products.edit', $product) }}" 
                               class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-900/20 dark:hover:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 rounded-lg text-sm font-medium transition">
                                <span>✏️</span>
                                <span>Edit</span>
                            </a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="flex-1">
                                @csrf @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('Hapus produk ini?')"
                                        class="w-full flex items-center justify-center gap-1.5 px-3 py-2 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/30 text-red-700 dark:text-red-400 rounded-lg text-sm font-medium transition">
                                    <span>🗑️</span>
                                    <span>Hapus</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $products->appends(request()->query())->links() }}
        </div>
    </div>
</x-app-layout>

