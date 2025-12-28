<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 py-6">
        <div class="flex flex-col gap-2">
            <div class="flex items-center justify-between gap-3">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">👥 Tim Fotokopiku</h1>
                <div class="flex items-center gap-3">
                    <div class="text-sm text-gray-600 dark:text-gray-400">#SemangatBerkarya #LayananCepat</div>
                    @if(($isOwner ?? false) || ($isAdmin ?? false) || ($isSystemAdmin ?? false))
                        <a href="{{ route('admin.staff.create') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                            ➕ Tambah Staff
                        </a>
                    @endif
                </div>
            </div>
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/30 dark:to-purple-900/30 border-l-4 border-indigo-600 dark:border-indigo-400 rounded-lg px-6 py-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="text-3xl">✨</div>
                    <div>
                        <div class="text-lg font-bold text-indigo-900 dark:text-indigo-100">Detail Matters. We Deliver.</div>
                        <div class="text-sm text-indigo-700 dark:text-indigo-300 mt-1">Tim profesional siap melayani kebutuhan fotokopi Anda dengan cepat dan berkualitas</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($staff as $s)
                @php
                    $rawPhone = preg_replace('/[^0-9]/', '', $s->phone ?? '');
                    $waNumber = $rawPhone ? '62' . ltrim($rawPhone, '0') : null;
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="relative">
                            <img src="{{ $s->photo ?? '/images/user-placeholder.svg' }}" alt="{{ $s->user->name }}" class="w-12 h-12 rounded-full object-cover border-2 border-indigo-200 dark:border-indigo-700">
                            <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full"></span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="font-semibold text-gray-900 dark:text-white truncate">{{ $s->user->name }}</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400 truncate">{{ $s->staff_id ?? 'ID Belum Tersedia' }}</div>
                        </div>
                    </div>

                    <div class="space-y-2 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Peran</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ ucfirst($s->role) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Jabatan</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $s->position ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-400">WhatsApp</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $s->phone ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700 space-y-3">
                        <div class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <span class="mt-0.5">📍</span>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">Jabodetabek</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $s->notes ?? 'Area layanan utama' }}</div>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @if($waNumber)
                                <a href="https://wa.me/{{ $waNumber }}" target="_blank" class="flex-1 min-w-[140px] inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-green-50 hover:bg-green-100 dark:bg-green-900/20 dark:hover:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg text-sm font-medium transition">
                                    💬 Chat WA
                                </a>
                            @endif
                            @if($rawPhone)
                                <a href="tel:{{ $rawPhone }}" class="flex-1 min-w-[140px] inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-900/20 dark:hover:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-lg text-sm font-medium transition">
                                    📞 Telepon
                                </a>
                            @endif
                        </div>
                        @if(($isOwner ?? false) || ($isAdmin ?? false) || ($isSystemAdmin ?? false) || $s->user_id === auth()->user()->id)
                            <a href="{{ route('admin.staff.edit', $s->id) }}" class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                                ✏️ Edit Profil
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Tidak ada data staff.</p>
            @endforelse
        </div>

        <div>
            {{ $staff->links() }}
        </div>
    </div>
</x-app-layout>
