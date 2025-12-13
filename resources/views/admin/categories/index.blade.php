<x-app-layout>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kategori</h1>
            <x-button href="{{ route('admin.categories.create') }}" color="indigo">Tambah</x-button>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100/70 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-700">
            @foreach ($categories as $cat)
                <div class="p-4 flex items-center justify-between">
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-white">{{ $cat->name }}</div>
                        <div class="text-sm text-gray-500">{{ $cat->description }}</div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('admin.categories.edit', $cat) }}" class="text-sm text-indigo-600">Edit</a>
                        <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="text-sm text-red-600">Hapus</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{ $categories->links() }}
    </div>
</x-app-layout>

