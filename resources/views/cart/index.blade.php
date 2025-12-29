<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4" x-data="cartPage()" x-init="recomputeTotals()">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Keranjang</h1>
            <a href="{{ route('home') }}" class="text-sm text-indigo-600">Tambah belanja</a>
        </div>

        @if ($cart->items->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center text-gray-600">
                Keranjang kosong.
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100/70 dark:border-gray-700 overflow-hidden">
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($cart->items as $item)
                        @php
                            $thumb = $item->product->thumbnail ? asset($item->product->thumbnail) : 'https://via.placeholder.com/120x120?text=Produk';
                        @endphp
                        <div class="p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4" data-item-id="{{ $item->id }}">
                            <div class="flex items-start sm:items-center gap-3 flex-1">
                                <img src="{{ $thumb }}" alt="{{ $item->product->name }}" class="w-16 h-16 rounded-lg object-cover bg-gray-100 flex-shrink-0">
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-gray-900 dark:text-white truncate">{{ $item->product->name }}</div>
                                    <div class="text-xs text-gray-500">Harga satuan Rp {{ number_format($item->product->price, 0, ',', '.') }}</div>
                                    <div class="mt-2 inline-flex items-center gap-2">
                                        <button type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-white hover:bg-gray-200 transition-colors" aria-label="Kurangi" @click="decrementQty({{ $item->id }})">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14" />
                                            </svg>
                                        </button>
                                        <input type="text" inputmode="numeric" pattern="[0-9]*" class="w-16 text-sm font-semibold text-center border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white py-1 px-1" x-model.number="items.find(i => i.id === {{ $item->id }})?.qty" @keydown="allowOnlyDigits($event)" @input="sanitizeQty({{ $item->id }}, $event)" @change="updateQtyDirect({{ $item->id }})" @keydown.enter.prevent="updateQtyDirect({{ $item->id }})">
                                        <button type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-600 text-white hover:bg-indigo-700 transition-colors" aria-label="Tambah" @click="incrementQty({{ $item->id }})">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 sm:ml-4 sm:flex-shrink-0">
                                <div class="font-semibold text-indigo-700 dark:text-indigo-300 item-subtotal" x-text="formatRupiah(items.find(i => i.id === {{ $item->id }})?.price * (items.find(i => i.id === {{ $item->id }})?.qty || 0))">Rp {{ number_format($item->qty * $item->product->price, 0, ',', '.') }}</div>
                                <form action="{{ route('cart.remove', $item) }}" method="POST" x-data @submit.once class="w-full sm:w-auto">
                                    @csrf
                                    <x-button type="submit" color="red" class="w-full sm:w-auto">Hapus</x-button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-100/70 dark:border-gray-700 p-4">
                <div>
                    <div class="text-sm text-gray-500">Total</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white"><span x-text="formatRupiah(subtotal)">Rp {{ number_format($cart->items->sum(fn($i) => $i->qty * $i->product->price), 0, ',', '.') }}</span></div>
                </div>
                <x-button href="{{ route('checkout.form') }}" color="indigo" class="w-full sm:w-auto">Checkout</x-button>
            </div>
        @endif
    </div>
    <script type="application/json" id="cart-items-json">@json($cart->items->map(fn($i) => ['id' => $i->id, 'price' => $i->product->price, 'qty' => $i->qty])->values())</script>
    <script>
        function cartPage() {
            const items = JSON.parse(document.getElementById('cart-items-json')?.textContent || '[]');
            return {
                items,
                subtotal: items.reduce((sum, i) => sum + i.price * i.qty, 0),
                formatRupiah(n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); },
                allowOnlyDigits(event) {
                    const allowed = ['Backspace','Tab','ArrowLeft','ArrowRight','Delete','Home','End'];
                    if (event.ctrlKey || event.metaKey || allowed.includes(event.key)) return;
                    if (!/^[0-9]$/.test(event.key)) event.preventDefault();
                },
                sanitizeQty(itemId, e) {
                    const item = this.items.find(i => i.id === itemId);
                    if (!item) return;
                    const digits = (e.target.value || '').replace(/[^0-9]/g, '');
                    e.target.value = digits;
                    item.qty = digits === '' ? '' : Number(digits);
                    this.updateItemSubtotalDisplay(itemId);
                    this.recomputeTotals();
                },
                async updateQtyDirect(itemId) {
                    const item = this.items.find(i => i.id === itemId);
                    if (!item) return;
                    const safe = Number(item.qty) || 1;
                    item.qty = safe < 1 ? 1 : safe;
                    this.updateItemSubtotalDisplay(itemId);
                    this.recomputeTotals();
                    await this.updateQty(itemId, 'set', item.qty);
                },
                async incrementQty(itemId) {
                    this.adjustQtyLocal(itemId, 1);
                    await this.updateQty(itemId, 'increment');
                },
                async decrementQty(itemId) {
                    this.adjustQtyLocal(itemId, -1);
                    await this.updateQty(itemId, 'decrement');
                },
                adjustQtyLocal(itemId, delta) {
                    const item = this.items.find(i => i.id === itemId);
                    if (!item) return;
                    const next = Math.max(1, Number(item.qty) + delta);
                    item.qty = next;
                    this.updateItemSubtotalDisplay(itemId);
                    this.recomputeTotals();
                },
                updateItemSubtotalDisplay(itemId) {
                    const item = this.items.find(i => i.id === itemId);
                    const el = document.querySelector(`[data-item-id="${itemId}"] .item-subtotal`);
                    if (item && el) el.textContent = this.formatRupiah(item.price * (Number(item.qty)||0));
                },
                recomputeTotals() {
                    this.subtotal = this.items.reduce((sum, i) => sum + (i.price * (Number(i.qty)||0)), 0);
                },
                async updateQty(itemId, action, value=null) {
                    try {
                        const response = await fetch(`/cart/qty/${itemId}`, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || document.querySelector('meta[name="csrf-token"]')?.content,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ action, value })
                        });
                        if (!response.ok) { console.warn('Gagal update qty'); return; }
                        const data = await response.json();
                        const item = this.items.find(i => i.id === itemId);
                        if (item) item.qty = data.qty;
                        if (data.removed) {
                            this.items = this.items.filter(i => i.id !== itemId);
                            const row = document.querySelector(`[data-item-id="${itemId}"]`);
                            if (row) row.remove();
                        }
                        this.subtotal = data.subtotal ?? this.subtotal;
                    } catch(e) { console.error(e); }
                }
            };
        }
    </script>
    <script src="//unpkg.com/alpinejs" defer></script>
</x-app-layout>

