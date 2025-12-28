<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 py-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">✏️ Edit Profil Staff</h1>
            <a href="{{ route('admin.staff.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">← Kembali</a>
        </div>

        @if(session('success'))
            <div class="bg-green-50 dark:bg-green-900/30 border-l-4 border-green-600 dark:border-green-400 rounded-lg px-6 py-4">
                <div class="text-sm text-green-800 dark:text-green-200">{{ session('success') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 dark:bg-red-900/30 border-l-4 border-red-600 dark:border-red-400 rounded-lg px-6 py-4">
                <div class="text-sm text-red-800 dark:text-red-200">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
            <form action="{{ route('admin.staff.update', $staff->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Profile Photo -->
                <div class="flex flex-col items-center gap-4 pb-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="relative">
                        <img id="photoPreview" src="{{ $staff->photo ?? '/images/user-placeholder.svg' }}" alt="Profile Photo" class="w-32 h-32 rounded-full object-cover border-4 border-indigo-200 dark:border-indigo-700">
                        <div class="absolute bottom-0 right-0 bg-indigo-600 text-white rounded-full p-2 cursor-pointer hover:bg-indigo-700 transition" onclick="document.getElementById('photoInput').click()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </div>
                    <input type="file" id="photoInput" name="photo" accept="image/*" class="hidden" onchange="previewPhoto(event)">
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $staff->user->name }}</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400">{{ $staff->staff_id ?? 'ID Belum Tersedia' }}</div>
                    </div>
                    @if($staff->photo && $staff->photo !== '/images/user-placeholder.svg')
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remove_photo" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                            <span class="text-sm text-red-600 dark:text-red-400">🗑️ Hapus foto profil</span>
                        </label>
                    @endif
                    <p class="text-xs text-gray-500 dark:text-gray-400 text-center">Klik ikon kamera untuk mengganti foto. Foto akan tetap ada sampai dihapus atau diganti.<br>(Max 2MB, Format: jpg, png, svg)</p>
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $staff->user->name) }}" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nomor WhatsApp</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $staff->phone) }}" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white" placeholder="081234567890">
                </div>

                <!-- Position -->
                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jabatan</label>
                    <input type="text" id="position" name="position" value="{{ old('position', $staff->position) }}" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Notes (Address) -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Alamat</label>
                    <textarea id="notes" name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">{{ old('notes', $staff->notes) }}</textarea>
                </div>

                <!-- Info Box -->
                <div class="bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-700 rounded-lg px-4 py-3">
                    <div class="flex items-start gap-2">
                        <span class="text-indigo-600 dark:text-indigo-400 text-lg">ℹ️</span>
                        <div class="text-sm text-indigo-800 dark:text-indigo-200">
                            <div class="font-medium mb-1">Informasi:</div>
                            <ul class="list-disc list-inside space-y-0.5 text-xs">
                                <li>Staff ID dan Role tidak dapat diubah</li>
                                <li>Foto profil akan disimpan di folder /public/images/staff/</li>
                                <li>Pastikan nomor WhatsApp aktif untuk komunikasi</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 pt-4">
                    <button type="submit" class="flex-1 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition">
                        💾 Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.staff.index') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg font-medium transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewPhoto(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('photoPreview').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
</x-app-layout>
