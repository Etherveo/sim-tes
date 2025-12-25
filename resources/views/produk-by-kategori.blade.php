@extends('layouts.app')

@section('content')
    <div class="container mx-auto max-w-7xl py-5 px-4 sm:px-6 lg:px-8">

        <h1 class="text-3xl font-bold text-gray-900 mb-5">
            Kategori: {{ $kategoriNama }}
        </h1>

        <div class="flex justify-between">
            <a href="{{ route('kategori.index') }}" class="text-indigo-600 hover:text-indigo-800 mb-5 inline-block">&larr; Lihat semua kategori</a>

            <div x-data="{ openSort: false }" class="relative text-left inline-flex justify-between mb-10">

                <button @click="openSort = !openSort" @click.away="openSort = false" class="inline-flex justify-between items-center w-full md:w-56 px-4 py-2 bg-white border border-gray-200 rounded-full text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none shadow-sm transition">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/></svg>
                        Urutkan
                    </span>
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div x-show="openSort" style="display: none;" class="absolute right-0 text-center z-30 mt-10 w-56 rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 overflow-hidden">
                    <div class="py-1">
                        @php $sUrl = fn($k, $d) => request()->fullUrlWithQuery(['sort_by' => $k, 'sort_direction' => $d]); @endphp
                        <a href="{{ $sUrl('nama', 'asc') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50">Nama (A-Z)</a>
                        <a href="{{ $sUrl('nama', 'desc') }}" class="border-t border-gray-400 block px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50">Nama (Z-A)</a>
                    </div>
                    <div class="py-1">
                        <a href="{{ $sUrl('harga', 'asc') }}" class="border-t border-gray-400 block px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50">Harga Termurah</a>
                        <a href="{{ $sUrl('harga', 'desc') }}" class="border-t border-gray-400 block px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50">Harga Termahal</a>
                    </div>
                    <div class="py-1">
                        <a href="{{ $sUrl('kode', 'desc') }}" class="border-t border-gray-400 block px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50">Produk Terbaru</a>
                    </div>
                </div>
            </div>
        </div>
        
        @if ($promoProducts->isNotEmpty())
            <div class="mb-16">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Promo Kategori Ini</h2>
                <div class="flex overflow-x-auto pb-6 -mx-4 px-4 sm:mx-0 sm:px-0 space-x-4 hide-scrollbar">
                    @foreach ($promoProducts as $product)
                        <div class="w-48 md:w-64 flex-shrink-0">
                            <x-product-card :product="$product" />
                        </div>
                    @endforeach

                </div>
            </div>
        @endif

        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Semua Produk Kategori Ini</h2>
        
        <div class="grid grid-cols-2 gap-4 md:gap-6 md:grid-cols-3 lg:grid-cols-4">
            
            @forelse ($categoryProducts as $product)
                <x-product-card :product="$product" />
            
            @empty
                <p class="col-span-full text-center text-gray-500">
                    Belum ada produk dalam kategori ini.
                </p>
            @endforelse

        </div>

        <div class="mt-12">
            {{ $categoryProducts->appends(request()->query())->links() }}
        </div>
    </div>
@endsection