@extends('layouts.app')

@section('content')
    <div class="w-full mx-auto py-12 px-4 sm:px-6 lg:px-16 xl:px-24">

        @if ($promoProducts->isNotEmpty())
            <div class="mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">Sedang Promo</h2>

                <div class="flex overflow-x-auto pb-6 -mx-4 px-4 sm:mx-0 sm:px-0 space-x-4 hide-scrollbar">
                    @foreach ($promoProducts as $product)
                        <div class="w-48 md:w-64 flex-shrink-0">
                            <x-product-card :product="$product" />
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($bestSellerProducts->isNotEmpty())
            <div class="mb-16">
                <div class="flex items-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900">Paling Laris</h2>
                    <span class="ml-3 px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-bold uppercase rounded-full">
                        Top 3
                    </span>
                </div>

                <div class="flex overflow-x-auto pb-6 -mx-4 px-4 sm:mx-0 sm:px-0 space-x-4 hide-scrollbar">
                    @foreach ($bestSellerProducts as $index => $product)
                        <div class="w-48 md:w-64 flex-shrink-0 relative pt-3 pl-3">

                            <div class="absolute top-0 left-0 z-20 w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-full text-white font-bold text-lg md:text-xl shadow-lg border-2 border-white
                                {{ $index == 0 ? 'bg-yellow-500' : ($index == 1 ? 'bg-gray-400' : 'bg-[#cd7f32]') }}">
                                #{{ $index + 1 }}
                            </div>

                            <div class="absolute top-4 right-0 z-20 bg-black/70 backdrop-blur-sm text-white text-[10px] md:text-xs font-bold px-2 py-1 rounded-l shadow-md">
                                Terjual {{ $product->total_sold }}
                            </div>

                            <div class="relative z-10 h-full">
                                <x-product-card :product="$product" />
                            </div>
                            
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <h2 class="text-3xl font-bold text-gray-900 mb-8">Katalog Produk</h2>
        
        <div class="grid grid-cols-2 gap-4 md:gap-6 md:grid-cols-3 lg:grid-cols-5">
            
            @forelse ($allProducts as $product)
                <x-product-card :product="$product" />
            
            @empty
                <p class="col-span-full text-center text-gray-500">
                    Belum ada produk yang tersedia saat ini.
                </p>
            @endforelse

        </div>
    </div>

    <style>
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
@endsection