@extends('layouts.app')

@section('title', 'Keranjang')

@section('content')
@php
    $qtyInput = 'dark:bg-dark-900 h-10 w-20 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90';

    // Data untuk perhitungan realtime di Alpine
    $cartData = $items->map(fn ($item) => [
        'price' => (int) $item->product->price,
        'stock' => (int) $item->product->stock,
        'available' => $item->product->isTersedia() && $item->product->stock >= 1,
        'saved' => (int) $item->quantity,
        'qty' => (int) $item->quantity,
    ])->values();

    // Route checkout dibuat di tahap berikutnya; tombol aktif otomatis kalau route-nya sudah ada
    $checkoutUrl = Route::has('pembeli.checkout.create') ? route('pembeli.checkout.create') : null;
@endphp

<div class="mb-6 flex items-center justify-between">
    <h1 class="text-xl font-semibold text-gray-800 dark:text-white/90">Keranjang</h1>
    <a href="{{ route('pembeli.catalog.index') }}" class="text-sm text-brand-500 hover:text-brand-600">
        + Lanjut belanja
    </a>
</div>

@include('partials.flash')

@if ($errors->any())
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        {{ $errors->first() }}
    </div>
@endif

<div x-data="{
    items: @js($cartData),
    rupiah(n) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(n);
    },
    qty(i) {
        return Math.max(parseInt(i.qty) || 0, 0);
    },
    valid(i) {
        const q = this.qty(i);
        return i.available && q >= 1 && q <= i.stock;
    },
    subtotal(i) {
        return this.valid(i) ? this.qty(i) * i.price : 0;
    },
    get total() {
        return this.items.reduce((sum, i) => sum + this.subtotal(i), 0);
    },
    get dirty() {
        return this.items.some(i => this.qty(i) !== i.saved);
    },
    get hasInvalid() {
        return this.items.some(i => !this.valid(i));
    },
    get canCheckout() {
        return this.items.length > 0 && !this.dirty && !this.hasInvalid;
    },
    clamp(i) {
        let q = parseInt(i.qty);
        if (isNaN(q) || q < 1) q = 1;
        if (i.stock >= 1 && q > i.stock) q = i.stock;
        i.qty = q;
    },
}">

<div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <table class="w-full text-left text-sm">
        <thead class="border-b border-gray-200 text-gray-500 dark:border-gray-800 dark:text-gray-400">
            <tr>
                <th class="px-5 py-3 font-medium">Produk</th>
                <th class="px-5 py-3 font-medium">Harga</th>
                <th class="px-5 py-3 font-medium">Jumlah</th>
                <th class="px-5 py-3 font-medium">Subtotal</th>
                <th class="px-5 py-3 text-right font-medium">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
            @forelse ($items as $item)
                @php $product = $item->product; @endphp
                <tr x-data="{ get it() { return items[{{ $loop->index }}]; } }">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            @if ($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                    class="h-12 w-12 rounded-lg object-cover" />
                            @else
                                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gray-100 text-xs text-gray-400 dark:bg-white/5">-</div>
                            @endif
                            <div>
                                <p class="text-gray-800 dark:text-white/90">{{ $product->name }}</p>
                                <p x-show="!it.available" style="display: none;" class="text-xs text-red-600">
                                    Produk tidak tersedia, hapus dari keranjang.
                                </p>
                                <p x-show="it.available && qty(it) > it.stock" style="display: none;" class="text-xs text-red-600">
                                    Stok tersisa <span x-text="it.stock"></span>, kurangi jumlahnya.
                                </p>
                                <p x-show="it.available && qty(it) <= it.stock && qty(it) !== it.saved" style="display: none;"
                                    class="text-xs text-amber-600">
                                    Belum disimpan, klik "Set".
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-gray-800 dark:text-white/90">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-3">
                        <form method="POST" action="{{ route('pembeli.cart.update', $item) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                                max="{{ max($product->stock, 1) }}" x-model.number="it.qty" @change="clamp(it)"
                                class="{{ $qtyInput }}" />
                            <button type="submit" class="text-brand-500 hover:text-brand-600">Set</button>
                        </form>
                    </td>
                    <td class="px-5 py-3 text-gray-800 dark:text-white/90">
                        <span x-text="rupiah(subtotal(it))">Rp {{ number_format($item->subtotal(), 0, ',', '.') }}</span>
                    </td>
                    <td class="px-5 py-3 text-right">
                        <form method="POST" action="{{ route('pembeli.cart.destroy', $item) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-700">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">
                        Keranjang masih kosong.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($items->isNotEmpty())
    <div class="mt-4 flex flex-col items-end gap-3">
        <div class="flex items-center gap-6">
            <p class="text-gray-600 dark:text-gray-400">Total</p>
            <p class="text-xl font-semibold text-gray-800 dark:text-white/90" x-text="rupiah(total)">
                Rp {{ number_format($total, 0, ',', '.') }}
            </p>
        </div>

        <p x-show="dirty" style="display: none;" class="text-xs text-amber-600">
            Ada jumlah yang belum disimpan. Klik "Set" pada produk terkait sebelum checkout.
        </p>
        <p x-show="!dirty && hasInvalid" style="display: none;" class="text-xs text-red-600">
            Ada produk yang tidak tersedia atau stoknya kurang. Perbaiki dulu sebelum checkout.
        </p>

        @if ($checkoutUrl)
            <a href="{{ $checkoutUrl }}"
                :aria-disabled="!canCheckout"
                :class="canCheckout ? '' : 'pointer-events-none opacity-50'"
                @click="if (!canCheckout) $event.preventDefault()"
                class="bg-brand-500 hover:bg-brand-600 rounded-lg px-6 py-3 text-sm font-medium text-white transition">
                Checkout
            </a>
        @else
            <button type="button" disabled
                class="bg-brand-500 cursor-not-allowed rounded-lg px-6 py-3 text-sm font-medium text-white opacity-50">
                Checkout
            </button>
            <p class="text-xs text-gray-500 dark:text-gray-400">Halaman checkout tersedia di tahap berikutnya.</p>
        @endif
    </div>
@endif

</div>
@endsection