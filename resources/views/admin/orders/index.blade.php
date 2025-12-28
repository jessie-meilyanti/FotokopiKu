<x-app-layout>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pesanan Pengguna</h1>
            <form method="GET" action="{{ route('admin.orders.index') }}" class="flex items-center gap-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 shadow-sm">
                <label for="status" class="text-sm text-gray-600 dark:text-gray-300">Filter status:</label>
                <select id="status" name="status" class="text-sm rounded-md border-gray-200 dark:border-gray-700 dark:bg-gray-900" onchange="this.form.submit()">
                    @php $statuses = ['' => 'Semua','pending' => 'Menunggu','processing' => 'Sedang diproses','completed' => 'Selesai','cancelled' => 'Dibatalkan']; @endphp
                    @foreach($statuses as $val => $label)
                        <option value="{{ $val }}" @selected(($status ?? '') === $val)> {{ $label }} </option>
                    @endforeach
                </select>
            </form>
        </div>

        <div id="ordersContainer" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100/70 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-700">
            @include('admin.orders._list', ['orders' => $orders])
        </div>

        {{ $orders->links() }}
    </div>

    <script>
        (function(){
            // polling admin orders feed to update list in near-real-time
            const feedUrl = "{{ route('admin.orders.feed', ['status' => $status ?? null]) }}";
            const container = document.getElementById('ordersContainer');
            let lastFetch = 0;

            async function fetchFeed(){
                try{
                    const res = await fetch(feedUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    if(!res.ok) return;
                    const data = await res.json();
                    if(!data.html) return;
                    // replace content with a smooth fade
                    container.innerHTML = data.html;
                    // add simple reveal
                    container.querySelectorAll('.transition').forEach(el=>el.classList.add('fade-in','show'));
                }catch(e){ console.warn('feed error', e); }
            }

            // initial poll after short delay so page can finish rendering
            let suppressFeedUntil = 0;
            let userInteracting = false;
            let interactionTimer = null;

            const pauseFor = (ms) => {
                suppressFeedUntil = Date.now() + ms;
            };

            // stop polling briefly after any form submission to avoid race that overwrites user updates
            container.addEventListener('submit', function(e){
                pauseFor(3000); // pause polling for 3s after submit
            }, true);

            // when the user focuses or types in any input/select/textarea inside container, suspend polling
            container.addEventListener('focusin', function(){
                userInteracting = true;
                if (interactionTimer) clearTimeout(interactionTimer);
            });
            container.addEventListener('focusout', function(){
                // resume after short delay to allow quick interactions
                if (interactionTimer) clearTimeout(interactionTimer);
                interactionTimer = setTimeout(()=>{ userInteracting = false; }, 2000);
            });

            // also pause when hovering over the list (desktop)
            container.addEventListener('mouseenter', function(){ userInteracting = true; if (interactionTimer) clearTimeout(interactionTimer); });
            container.addEventListener('mouseleave', function(){ if (interactionTimer) clearTimeout(interactionTimer); interactionTimer = setTimeout(()=>{ userInteracting = false; }, 1000); });

            // treat typing or changing inputs as interaction and briefly pause poll
            container.addEventListener('input', function(){ pauseFor(5000); }, true);
            container.addEventListener('change', function(){ pauseFor(3000); }, true);

            setTimeout(function(){ if(Date.now() >= suppressFeedUntil && !userInteracting) fetchFeed(); }, 3000);
            // poll every 12 seconds but skip if suppressed or user interacting
            setInterval(function(){ if(Date.now() < suppressFeedUntil || userInteracting) return; fetchFeed(); }, 12000);
        })();
    </script>
</x-app-layout>

