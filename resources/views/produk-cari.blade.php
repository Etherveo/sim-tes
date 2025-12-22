@extends('layouts.app')

@section('content')
    <div class="container mx-auto max-w-7xl py-12 px-4 sm:px-6 lg:px-8">
        
        <h1 class="text-3xl font-bold text-gray-900 mb-8">
            Hasil pencarian untuk: "{{ $query }}"
        </h1>
        
        <div class="grid grid-cols-2 gap-4 md:gap-6 md:grid-cols-3 lg:grid-cols-4">
            
            @forelse ($products as $product)
                <x-product-card :product="$product" />
            
            @empty
                <p class="col-span-full text-center text-gray-500 text-lg">
                    Maaf, tidak ada produk yang ditemukan untuk kata kunci "{{ $query }}".
                </p>
            @endforelse

        </div>
    </div>
@endsection