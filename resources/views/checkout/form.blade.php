<x-app-layout>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Checkout</h1>
            <a href="{{ route('cart.index') }}" class="text-sm text-indigo-600 hover:underline">Kembali ke keranjang</a>
        </div>

        <div class="grid md:grid-cols-3 gap-6" x-data="checkoutForm()" x-init="calculateTotal()">
            <div class="md:col-span-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-100/70 dark:border-gray-700 p-4 sm:p-5 space-y-4">
                <h2 class="font-semibold text-gray-900 dark:text-white">Detail Pengiriman & Pembayaran</h2>
                <form action="{{ route('checkout.process') }}" method="POST" enctype="multipart/form-data" class="space-y-4" @submit="validateForm">
                    @csrf
                    <div>
                        <label class="text-sm text-gray-600 dark:text-gray-300 block mb-1">
                            👤 Nama lengkap penerima <span class="text-red-500 text-sm">*</span>
                        </label>
                        <input type="text" name="recipient" placeholder="Nama lengkap" class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900 transition focus:ring-2 focus:ring-indigo-200" value="{{ old('recipient') }}" required>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600 dark:text-gray-300 block mb-1">
                            📲 No. WhatsApp <span class="text-red-500 text-sm">*</span>
                        </label>
                        <input type="text" name="whatsapp" placeholder="08xxxx" class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900 transition focus:ring-2 focus:ring-indigo-200" value="{{ old('whatsapp') }}" required>
                    </div>

                    <div>
                        <label class="text-sm text-gray-600 dark:text-gray-300 block mb-1">
                            🚚 Metode Pengiriman <span class="text-red-500 text-sm">*</span>
                        </label>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <label class="flex items-center space-x-2 cursor-pointer flex-1">
                                <input type="radio" name="delivery_method" value="ambil-di-tempat" class="text-indigo-600" x-model="deliveryMethod" @change="onDeliveryChange" checked>
                                <span class="text-sm sm:text-base">Ambil di Tempat (Gratis)</span>
                            </label>
                            <label class="flex items-center space-x-2 flex-1" :class="totalQty >= 10 ? 'cursor-pointer' : 'opacity-50 cursor-not-allowed'">
                                <input type="radio" name="delivery_method" value="kirim-antar" class="text-indigo-600" x-model="deliveryMethod" @change="onDeliveryChange" :disabled="totalQty < 10">
                                <span class="text-sm sm:text-base">Kirim-Antar (min. 10 item)</span>
                            </label>
                        </div>
                        <p x-show="totalQty < 10" class="text-xs text-red-500 mt-1" x-text="'Tambah minimal ' + (10 - totalQty) + ' item lagi untuk kirim-antar'"></p>
                        <p class="text-xs text-gray-500 mt-1">📍 Alamat toko: Ruko Summarecon, Bekasi</p>
                    </div>

                    <div x-show="deliveryMethod === 'kirim-antar'" x-transition>
                        <label class="text-sm text-gray-600 dark:text-gray-300 block mb-1">
                            🏙️ Kota Tujuan <span class="text-red-500 text-sm">*</span>
                        </label>
                        <select name="city" x-model="city" @change="calculateTotal" class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900 transition focus:ring-2 focus:ring-indigo-200" required>
                            <option value="">Pilih kota</option>
                            <option value="bekasi">Bekasi (Rp 12.000)</option>
                            <option value="jakarta">Jakarta (Rp 15.000)</option>
                            <option value="depok">Depok (Rp 18.000)</option>
                            <option value="bogor">Bogor (Rp 20.000)</option>
                            <option value="tangerang">Tangerang (Rp 22.000)</option>
                            <option value="luar">Luar Jabodetabek (Rp 25.000)</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Ongkir terjangkau se-Jabodetabek</p>
                    </div>

                    <div x-show="deliveryMethod === 'kirim-antar'" x-transition>
                        <label class="text-sm text-gray-600 dark:text-gray-300 block mb-1">
                            📍 Alamat lengkap <span class="text-red-500 text-sm">*</span>
                        </label>
                        <textarea name="address" rows="3" placeholder="Jalan, RT/RW, Kelurahan, Kota" class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900 transition focus:ring-2 focus:ring-indigo-200" required>{{ old('address') }}</textarea>
                    </div>

                    <div>
                        <label class="text-sm text-gray-600 dark:text-gray-300 block mb-1">
                            💳 Metode Pembayaran <span class="text-red-500 text-sm">*</span>
                        </label>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <label class="flex items-center space-x-2 cursor-pointer flex-1">
                                <input type="radio" name="payment_method" value="COD" class="text-indigo-600" x-model="paymentMethod" checked>
                                <span class="text-sm sm:text-base">Bayar Di Tempat</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer flex-1">
                                <input type="radio" name="payment_method" value="QRIS" class="text-indigo-600" x-model="paymentMethod">
                                <span class="text-sm sm:text-base">QRIS</span>
                            </label>
                        </div>
                    </div>

                    <div x-show="paymentMethod === 'QRIS'" x-transition class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg space-y-3">
                        <h3 class="font-semibold text-sm">💠 Scan QRIS FotokopiKu</h3>
                        <div class="flex justify-center">
                            <img src="/images/qris-fotokopiku.svg" alt="QRIS FotokopiKu" class="w-48 h-48 border border-gray-200 rounded-lg">
                        </div>
                        <p class="text-xs text-gray-500 text-center">ID: 9360037800099999 | NMID: ID10220312345678</p>
                        <div>
                            <label class="text-sm text-gray-600 dark:text-gray-300 block mb-1">
                                Upload Bukti Transfer <span class="text-red-500 text-sm">*</span>
                            </label>
                            <input type="file" name="payment_proof" accept="image/*" class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                            <p class="text-xs text-gray-500 mt-1">Admin akan validasi pembayaran Anda</p>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed" :disabled="isSubmitting">
                            <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isSubmitting ? 'Memproses...' : 'Buat Pesanan'">Buat Pesanan</span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100/70 dark:border-gray-700 p-4 sm:p-5 space-y-3 md:h-fit md:sticky md:top-4">
                <h2 class="font-semibold text-gray-900 dark:text-white">Ringkasan Pesanan</h2>
                @php
                    $subtotal = $cart->items->sum(fn($i) => $i->qty * $i->product->price);
                    $totalQty = $cart->items->sum(fn($i) => $i->qty);
                @endphp
                <div class="divide-y divide-gray-100 dark:divide-gray-700 max-h-96 overflow-y-auto" x-data x-ref="itemsContainer">
                    @foreach ($cart->items as $item)
                        <div class="py-3 flex gap-2 items-start" data-item-id="{{ $item->id }}" data-price="{{ $item->product->price }}">
                            <img src="{{ $item->product->thumbnail ?? 'https://via.placeholder.com/60x60?text=Produk' }}" alt="{{ $item->product->name }}" class="w-12 h-12 rounded-md object-cover flex-shrink-0">
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-gray-900 dark:text-white text-sm truncate">{{ $item->product->name }}</div>
                                <div class="text-xs text-gray-500 mb-1">Rp {{ number_format($item->product->price, 0, ',', '.') }}</div>
                                <div class="inline-flex items-center gap-1 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/60 px-2 py-1">
                                    <button type="button" class="px-2 py-1 text-gray-600 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition rounded" @click="decrementQty({{ $item->id }})">−</button>
                                    <input type="text" inputmode="numeric" pattern="[0-9]*" min="1" class="w-14 text-sm font-semibold text-center border-0 bg-transparent py-1 focus:ring-0 focus:outline-none appearance-none text-gray-900 dark:text-white" x-model.number="items.find(i => i.id === {{ $item->id }})?.qty" @keydown="allowOnlyDigits($event)" @input="sanitizeQty({{ $item->id }}, $event)" @change="updateQtyDirect({{ $item->id }}, true)" @keydown.enter.prevent="updateQtyDirect({{ $item->id }}, true)">
                                    <button type="button" class="px-2 py-1 text-gray-600 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition rounded" @click="incrementQty({{ $item->id }})">+</button>
                                </div>
                            </div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-gray-100 text-right flex-shrink-0 item-subtotal">Rp {{ number_format($item->qty * $item->product->price, 0, ',', '.') }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="flex justify-between pt-3 text-sm text-gray-500">
                    <span>Subtotal (<span x-text="totalQty">{{ $totalQty }}</span> item)</span>
                    <span x-text="'Rp ' + subtotalDisplay">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm" :class="shipping > 0 ? 'text-gray-700 dark:text-gray-200' : 'text-gray-500'">
                    <span>Ongkir</span>
                    <span x-text="'Rp ' + shippingDisplay" class="font-semibold">Rp {{ $subtotal > 0 && request()->get('city') ? '0' : '0' }}</span>
                </div>
                <div class="flex justify-between pt-2 text-lg font-semibold border-t dark:border-gray-700">
                    <span>Total</span>
                    <span x-text="'Rp ' + totalDisplay" class="text-indigo-600 dark:text-indigo-400">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <script type="application/json" id="cart-items-json">@json($cart->items->map(fn($i) => ['id' => $i->id, 'price' => $i->product->price, 'qty' => $i->qty])->values())</script>
    <script>
        function checkoutForm() {
            const shippingRates = {
                'bekasi': 12000,
                'jakarta': 15000,
                'depok': 18000,
                'bogor': 20000,
                'tangerang': 22000,
                'luar': 25000
            };

            const items = JSON.parse(document.getElementById('cart-items-json')?.textContent || '[]');
            let subtotal = items.reduce((sum, item) => sum + (item.price * item.qty), 0);

            return {
                deliveryMethod: 'ambil-di-tempat',
                paymentMethod: 'COD',
                city: '',
                shipping: 0,
                subtotal: subtotal,
                totalQty: items.reduce((sum, item) => sum + item.qty, 0),
                items: items,
                isSubmitting: false,
                qtyInputTimeouts: {},

                get subtotalDisplay() {
                    return this.subtotal.toLocaleString('id-ID');
                },

                get shippingDisplay() {
                    return this.shipping.toLocaleString('id-ID');
                },

                get totalDisplay() {
                    return (this.subtotal + this.shipping).toLocaleString('id-ID');
                },

                onDeliveryChange() {
                    if (this.deliveryMethod === 'ambil-di-tempat') {
                        this.shipping = 0;
                        this.city = '';
                    } else if (this.totalQty < 10) {
                        alert('Minimal 10 item untuk kirim-antar');
                        this.deliveryMethod = 'ambil-di-tempat';
                        return;
                    }
                    this.calculateTotal();
                },

                calculateTotal() {
                    if (this.deliveryMethod === 'kirim-antar' && this.city) {
                        this.shipping = shippingRates[this.city] || 25000;
                    } else {
                        this.shipping = 0;
                    }
                },

                async incrementQty(itemId) {
                    this.adjustQtyLocal(itemId, 1);
                    await this.updateQty(itemId, 'increment');
                },

                async decrementQty(itemId) {
                    this.adjustQtyLocal(itemId, -1);
                    await this.updateQty(itemId, 'decrement');
                },

                async updateQtyDirect(itemId) {
                    const item = this.items.find(i => i.id === itemId);
                    if (!item) return;

                    const safeQty = Number(item.qty) || 1;
                    item.qty = safeQty < 1 ? 1 : safeQty;

                    this.updateItemSubtotalDisplay(itemId);
                    this.recomputeTotals();

                    await this.updateQty(itemId, 'set', item.qty);
                },

                sanitizeQty(itemId, event) {
                    const item = this.items.find(i => i.id === itemId);
                    if (!item) return;
                    const digits = (event.target.value || '').replace(/[^0-9]/g, '');
                    event.target.value = digits;
                    item.qty = digits === '' ? '' : Number(digits);
                },

                allowOnlyDigits(event) {
                    const allowedKeys = ['Backspace', 'Tab', 'ArrowLeft', 'ArrowRight', 'Delete', 'Home', 'End'];
                    if (event.ctrlKey || event.metaKey || allowedKeys.includes(event.key)) return;
                    if (!/^[0-9]$/.test(event.key)) {
                        event.preventDefault();
                    }
                },

                adjustQtyLocal(itemId, delta) {
                    const item = this.items.find(i => i.id === itemId);
                    if (!item) return;
                    const next = Math.max(1, Number(item.qty) + delta);
                    item.qty = next;
                    this.updateItemSubtotalDisplay(itemId);
                    this.recomputeTotals();
                },

                recomputeTotals() {
                    this.subtotal = this.items.reduce((sum, i) => sum + (i.price * (Number(i.qty) || 0)), 0);
                    this.totalQty = this.items.reduce((sum, i) => sum + (Number(i.qty) || 0), 0);
                    // Ensure delivery constraint stays consistent
                    if (this.totalQty < 10 && this.deliveryMethod === 'kirim-antar') {
                        this.deliveryMethod = 'ambil-di-tempat';
                        this.shipping = 0;
                        this.city = '';
                    }
                    this.calculateTotal();
                },

                updateItemSubtotalDisplay(itemId) {
                    const item = this.items.find(i => i.id === itemId);
                    const itemEl = document.querySelector(`[data-item-id="${itemId}"]`);
                    if (!item || !itemEl) return;
                    const subtotalEl = itemEl.querySelector('.item-subtotal');
                    if (subtotalEl) {
                        subtotalEl.textContent = 'Rp ' + (item.price * (Number(item.qty) || 0)).toLocaleString('id-ID');
                    }
                },

                async updateQty(itemId, action, value = null) {
                    try {
                        const response = await fetch(`/cart/qty/${itemId}`, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ action, value })
                        });

                        if (!response.ok) {
                            console.warn('Gagal mengubah jumlah item');
                            return;
                        }

                        const data = await response.json();
                        const item = this.items.find(i => i.id === itemId);
                        if (item) {
                            item.qty = data.qty;
                        }
                        
                        if (data.qty === 0) {
                            this.items = this.items.filter(i => i.id !== itemId);
                            const itemEl = document.querySelector(`[data-item-id="${itemId}"]`);
                            if (itemEl) itemEl.remove();
                        }

                        this.subtotal = (data.subtotal ?? this.items.reduce((sum, i) => sum + (i.price * i.qty), 0));
                        this.totalQty = (data.totalQty ?? this.items.reduce((sum, i) => sum + i.qty, 0));

                        this.updateItemSubtotalDisplay(itemId);
                        
                        // Reset delivery method if qty drops below 10
                        if (this.totalQty < 10 && this.deliveryMethod === 'kirim-antar') {
                            this.deliveryMethod = 'ambil-di-tempat';
                            this.shipping = 0;
                            this.city = '';
                        }
                        
                        this.calculateTotal();
                    } catch (error) {
                        console.error('Error:', error);
                    }
                },

                validateForm(e) {
                    if (this.isSubmitting) {
                        e.preventDefault();
                        return false;
                    }
                    if (this.deliveryMethod === 'kirim-antar' && !this.city) {
                        e.preventDefault();
                        alert('Pilih kota tujuan terlebih dahulu');
                        return false;
                    }
                    this.isSubmitting = true;
                }
            };
        }
    </script>
    <script src="//unpkg.com/alpinejs" defer></script>
</x-app-layout>
