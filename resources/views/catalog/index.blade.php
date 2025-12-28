<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <div class="grid lg:grid-cols-2 gap-6 items-center">
            <div class="space-y-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 text-xs font-semibold">Print & ATK Harga Terjangkau</span>
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white leading-tight">
                    Fotokopi, jasa print, laminating, jilid, dan alat tulis untuk kantor & sekolah.
                </h1>
                <p class="text-gray-600 dark:text-gray-300">
                    🚀 Pesan online, tanpa antri di tempat. Semua siap cepat dengan harga terjangkau se-Jabodetabek. 
                    <strong>📍 Lokasi: Ruko Summarecon, Bekasi.</strong>
                </p>
                <div class="flex flex-wrap gap-3">
                    <x-button href="#produk" color="indigo">Belanja sekarang</x-button>
                    <x-button href="#jasa" color="gray">Lihat jasa cetak</x-button>
                </div>
            </div>
            <div class="relative rounded-2xl overflow-hidden shadow-lg">
                <div class="aspect-[16/9] md:aspect-[5/3]">
                    <img src="/images/Toko.jpg" alt="Hero" class="w-full h-full object-cover">
                </div>
                <div class="absolute inset-0 bg-gradient-to-tr from-indigo-900/10 to-transparent"></div>
            </div>
        </div>

        <div id="jasa" class="grid sm:grid-cols-3 gap-4">
            @php
                $services = [
                    ['title' => 'Print & Fotokopi', 'desc' => 'Hitam/warna, A4-F4-A3', 'icon' => '🖨️'],
                    ['title' => 'Laminating & Jilid', 'desc' => 'Buku, skripsi, laporan', 'icon' => '📚'],
                    ['title' => 'ATK & Kantor', 'desc' => 'Kertas, pena, map, dll', 'icon' => '✏️'],
                ];
            @endphp
            @foreach ($services as $item)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100/70 dark:border-gray-700 p-4 flex space-x-3">
                    <div class="text-2xl">{{ $item['icon'] }}</div>
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $item['title'] }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-300">{{ $item['desc'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <div id="produk" class="bg-white dark:bg-gray-800/80 rounded-2xl shadow-sm border border-gray-100/70 dark:border-gray-700 p-6">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Katalog Produk & Jasa</h2>
                    <p class="text-sm text-gray-500">Filter kategori untuk mempercepat pencarian.</p>
                </div>
                <form method="GET" class="flex flex-wrap items-center gap-2">
                    <select name="category" onchange="this.form.submit()" class="rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                        <option value="">Semua kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->slug }}" @selected($categorySlug === $cat->slug)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @if($categorySlug)
                        <a href="{{ route('home') }}" class="text-sm text-indigo-600">Reset</a>
                    @endif
                </form>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($products as $product)
                    <div class="group bg-white dark:bg-gray-900 rounded-2xl border border-gray-100/80 dark:border-gray-700 shadow-sm hover:shadow-lg transition overflow-hidden flex flex-col">
                        <div class="relative aspect-[4/3] overflow-hidden">
                            <img src="{{ $product->thumbnail ?: '/images/product-placeholder.svg' }}" alt="{{ $product->name }}" loading="lazy" class="w-full h-full object-cover transition duration-300 group-hover:scale-[1.03]">
                            <div class="absolute top-3 left-3 px-3 py-1 rounded-full bg-white/90 dark:bg-gray-900/90 text-xs font-semibold text-gray-800 dark:text-gray-100 shadow">
                                {{ $product->category?->name ?? 'Produk & Jasa' }}
                            </div>
                        </div>
                        <div class="p-4 flex-1 flex flex-col gap-2">
                            <a href="{{ route('produk.show', $product->slug) }}" class="font-semibold text-gray-900 dark:text-white text-lg leading-tight line-clamp-2">
                                {{ $product->name }}
                            </a>
                            <p class="text-sm text-gray-500 dark:text-gray-300 line-clamp-2">{{ $product->description }}</p>
                            <div class="mt-auto flex items-center justify-between pt-2">
                                <div class="text-lg font-bold text-indigo-700 dark:text-indigo-300" aria-label="Harga">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </div>
                                @auth
                                    @if(auth()->user()->is_admin || auth()->user()->staff)
                                        <x-button href="{{ route('admin.products.edit', $product) }}" color="indigo">Edit</x-button>
                                    @else
                                        <form action="{{ route('cart.add', $product) }}" method="POST">
                                            @csrf
                                            <x-button type="submit" color="indigo">Tambah</x-button>
                                        </form>
                                    @endif
                                @else
                                    <x-button href="{{ route('login') }}" color="indigo">Masuk untuk beli</x-button>
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

