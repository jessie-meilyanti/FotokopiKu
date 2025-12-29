<!-- Floating Chat Button - Appears for authenticated users (non-admin) -->
@auth
  @if (!auth()->user()?->is_admin)
    <div class="fixed bottom-6 right-6 z-40 group" x-data="{ hover: false }">
      <!-- Tooltip -->
      <div class="absolute bottom-full right-0 mb-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none">
        <div class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-sm px-3 py-2 rounded-lg whitespace-nowrap font-medium shadow-lg">
          Tanya Admin di WhatsApp
          <div class="absolute bottom-0 right-3 transform translate-y-full w-0 h-0 border-l-8 border-r-8 border-t-8 border-l-transparent border-r-transparent border-t-gray-900 dark:border-t-white"></div>
        </div>
      </div>

      <!-- Button -->
      <a href="https://wa.me/62812345678?text=Halo%20Admin%20FotokopiKu%2C%20saya%20ingin%20bertanya%20tentang..." 
         target="_blank" 
         rel="noopener noreferrer"
         class="flex items-center justify-center w-14 h-14 rounded-full bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 group-hover:ring-4 group-hover:ring-indigo-400 dark:group-hover:ring-indigo-300"
         title="Hubungi Admin via WhatsApp">
        <!-- Phone Icon SVG -->
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
        </svg>
      </a>
    </div>
  @endif
@endauth
