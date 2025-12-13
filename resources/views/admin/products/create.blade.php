<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah Produk/Jasa</h1>
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100/70 dark:border-gray-700 p-4 space-y-3">
            @csrf
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-600">Nama</label>
                    <input type="text" name="name" required placeholder="Contoh: Kertas A4 80gsm" class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                </div>
                <div>
                    <label class="text-sm text-gray-600">Kategori</label>
                    <select name="category_id" class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                        <option value="">Pilih</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm text-gray-600">Harga</label>
                    <input type="number" name="price" required min="0" placeholder="Rp" class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                </div>
                <div>
                    <label class="text-sm text-gray-600">Stok</label>
                    <input type="number" name="stock" required min="0" placeholder="0-999" class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                </div>
            </div>
            <div>
                <label class="text-sm text-gray-600">URL Thumbnail</label>
                <input type="url" name="thumbnail" class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900" placeholder="https://via.placeholder.com/600x400?text=Produk">
                <div class="text-xs text-gray-500 mt-1">atau unggah gambar:</div>
                <input type="file" name="thumbnail_file" class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900">
            </div>
            <div>
                <label class="text-sm text-gray-600">Deskripsi</label>
                <textarea name="description" rows="4" placeholder="Ringkasan fitur, ukuran, warna..." class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900"></textarea>
            </div>
            <div class="flex items-center space-x-2">
                <input type="checkbox" name="is_service" value="1" class="rounded border-gray-300 text-indigo-600">
                <label class="text-sm text-gray-700 dark:text-gray-200">Ini layanan (tanpa stok)</label>
            </div>
            <div class="flex justify-end">
                <x-button type="submit" color="indigo">Simpan</x-button>
            </div>
        </form>
    </div>
</x-app-layout>

