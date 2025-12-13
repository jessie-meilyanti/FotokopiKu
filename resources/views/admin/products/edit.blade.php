<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Produk/Jasa</h1>
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100/70 dark:border-gray-700 p-4 space-y-3">
            @csrf @method('PUT')
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-600">Nama</label>
                    <input type="text" name="name" required placeholder="Contoh: Kertas A4 80gsm" class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900" value="{{ old('name', $product->name) }}">
                </div>
                <div>
                    <label class="text-sm text-gray-600">Kategori</label>
                    <select name="category_id" class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                        <option value="">Pilih</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" @selected($product->category_id == $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm text-gray-600">Harga</label>
                    <input type="number" name="price" required min="0" placeholder="Rp" class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900" value="{{ old('price', $product->price) }}">
                </div>
                <div>
                    <label class="text-sm text-gray-600">Stok</label>
                    <input type="number" name="stock" required min="0" placeholder="0-999" class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900" value="{{ old('stock', $product->stock) }}">
                </div>
            </div>
            <div>
                <label class="text-sm text-gray-600">URL Thumbnail</label>
                <input type="url" name="thumbnail" class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900" value="{{ old('thumbnail', $product->thumbnail) }}">
                <div class="text-xs text-gray-500 mt-1">atau unggah gambar baru:</div>
                <input type="file" name="thumbnail_file" class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900">
            </div>
            <div>
                <label class="text-sm text-gray-600">Deskripsi</label>
                <textarea name="description" rows="4" placeholder="Ringkasan fitur, ukuran, warna..." class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900">{{ old('description', $product->description) }}</textarea>
            </div>
            <div class="flex items-center space-x-2">
                <input type="checkbox" name="is_service" value="1" class="rounded border-gray-300 text-indigo-600" @checked($product->is_service)>
                <label class="text-sm text-gray-700 dark:text-gray-200">Ini layanan (tanpa stok)</label>
            </div>
            <div class="flex justify-end">
                <x-button type="submit" color="indigo">Update</x-button>
            </div>
        </form>
    </div>
</x-app-layout>

