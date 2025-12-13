<x-app-layout>
    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah Kategori</h1>
        <form action="{{ route('admin.categories.store') }}" method="POST" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100/70 dark:border-gray-700 p-4 space-y-3">
            @csrf
            <div>
                <label class="text-sm text-gray-600">Nama</label>
                <input type="text" name="name" class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900" required>
            </div>
            <div>
                <label class="text-sm text-gray-600">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900"></textarea>
            </div>
            <div class="flex justify-end">
                <x-button type="submit" color="indigo">Simpan</x-button>
            </div>
        </form>
    </div>
</x-app-layout>

