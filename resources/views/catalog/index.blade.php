<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <div class="grid lg:grid-cols-2 gap-6 items-center">
            <div class="space-y-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 text-xs font-semibold">Print & ATK Terjangkau</span>
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white leading-tight">
                    Fotokopi, jasa print, laminating, jilid, dan alat tulis untuk kantor & sekolah.
                </h1>
                <p class="text-gray-600 dark:text-gray-300">
                    Pesan online, tanpa antri di tempat. Semua siap cepat dengan harga bersahabat.
                </p>
                <div class="flex flex-wrap gap-3">
                    <x-button href="#produk" color="indigo">Belanja sekarang</x-button>
                    <x-button href="#jasa" color="gray">Lihat jasa cetak</x-button>
                </div>
            </div>
            <div class="relative">
                <img src="/images/Toko.jpg" alt="Hero" class="rounded-2xl shadow-lg w-full h-64 md:h-96 object-cover">
                <div class="absolute inset-0 bg-gradient-to-tr from-indigo-900/10 to-transparent rounded-2xl"></div>
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
                    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100/60 dark:border-gray-700 shadow-sm flex flex-col">
                        <img src="{{ $product->thumbnail ?? 'https://via.placeholder.com/600x400?text=Produk' }}" alt="{{ $product->name }}" class="h-44 w-full object-cover rounded-t-xl">
                        <div class="p-4 flex-1 flex flex-col">
                            <div class="text-xs uppercase text-gray-500">{{ $product->category?->name }}</div>
                            <a href="{{ route('produk.show', $product->slug) }}" class="font-semibold text-gray-900 dark:text-white mt-1 line-clamp-2">{{ $product->name }}</a>
                            <p class="text-sm text-gray-500 dark:text-gray-300 line-clamp-2 mt-1">{{ $product->description }}</p>
                            <div class="mt-auto flex items-center justify-between pt-3">
                                <div class="font-bold text-indigo-700 dark:text-indigo-300">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </div>
                                @auth
                                    <form action="{{ route('cart.add', $product) }}" method="POST">
                                        @csrf
                                        <x-button type="submit" color="indigo">Tambah</x-button>
                                    </form>
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

