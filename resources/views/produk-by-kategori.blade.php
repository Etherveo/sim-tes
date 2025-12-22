@extends('layouts.app')

@section('content')
    <div class="container mx-auto max-w-7xl py-12 px-4 sm:px-6 lg:px-8">
        
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            Kategori: {{ $kategoriNama }}
        </h1>
        <a href="{{ route('kategori.index') }}" class="text-indigo-600 hover:text-indigo-800 mb-8 inline-block">&larr; Lihat semua kategori</a>

        
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
    </div>
@endsection