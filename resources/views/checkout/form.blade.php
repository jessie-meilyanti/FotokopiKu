<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Checkout</h1>
            <a href="{{ route('cart.index') }}" class="text-sm text-indigo-600">Kembali ke keranjang</a>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            <div class="md:col-span-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-100/70 dark:border-gray-700 p-5 space-y-4">
                <h2 class="font-semibold text-gray-900 dark:text-white">Detail Pengiriman</h2>
                <form action="{{ route('checkout.process') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="text-sm text-gray-600">Nama penerima</label>
                        <input type="text" name="recipient" required placeholder="Nama lengkap" class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900 transition focus:ring-2 focus:ring-indigo-200" value="{{ old('recipient') }}">
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Telepon</label>
                        <input type="text" name="phone" required placeholder="08xxxx" class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900 transition focus:ring-2 focus:ring-indigo-200" value="{{ old('phone') }}">
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Alamat lengkap</label>
                        <textarea name="address" rows="3" required placeholder="Jalan, RT/RW, Kelurahan, Kota" class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900 transition focus:ring-2 focus:ring-indigo-200">{{ old('address') }}</textarea>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Metode bayar</label>
                        <select name="payment_method" class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900 transition focus:ring-2 focus:ring-indigo-200">
                            <option value="COD">COD / Bayar di tempat</option>
                            <option value="Transfer">Transfer</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Bank Transfer (opsional)</label>
                        <input type="text" name="payment_bank" placeholder="BCA 123xxxx a.n FotokopiKu" class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900 transition focus:ring-2 focus:ring-indigo-200" value="{{ old('payment_bank') }}">
                    </div>
                    <div class="pt-2">
                        <x-button type="submit" color="indigo">Buat Pesanan</x-button>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100/70 dark:border-gray-700 p-5 space-y-3">
                <h2 class="font-semibold text-gray-900 dark:text-white">Ringkasan</h2>
                @php
                    $subtotal = $cart->items->sum(fn($i) => $i->qty * $i->product->price);
                @endphp
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($cart->items as $item)
                        <div class="py-3 flex justify-between text-sm">
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">{{ $item->product->name }}</div>
                                <div class="text-gray-500">Qty {{ $item->qty }}</div>
                            </div>
                            <div class="text-gray-800 dark:text-gray-100">
                                Rp {{ number_format($item->qty * $item->product->price, 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="flex justify-between pt-3 text-sm text-gray-500">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Ongkir</span>
                    <span>Rp 0</span>
                </div>
                <div class="flex justify-between pt-2 text-lg font-semibold">
                    <span>Total</span>
                    <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

