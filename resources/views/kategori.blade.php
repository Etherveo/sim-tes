@extends('layouts.app')

@section('content')
    <div class="w-full mx-auto py-12 px-4 sm:px-6 lg:px-16 xl:px-24">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Daftar Kategori</h1>
        
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 md:gap-6">
            
            @foreach ($categories as $category)
                <a href="{{ route('produk.by.kategori', ['kategori_slug' => $category->slug]) }}" 
                   class="group flex flex-col rounded-lg shadow-sm hover:shadow-lg transition-shadow duration-300 overflow-hidden border border-gray-200 h-full bg-white">

                    <div class="h-32 w-full bg-white flex items-center justify-center p-4 border-b border-gray-100">
                        @if($category->gambar)
                            <img src="{{ asset('storage/' . $category->gambar) }}" 
                                 alt="{{ $category->nama_kategori }}" 
                                 class="w-full h-full rounded-md object-contain group-hover:scale-110 transition-transform duration-300">
                        @else
                            <div class="flex flex-col items-center justify-center text-gray-300">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        @endif
                    </div>

                    <div class="bg-gray-50 p-3 flex items-center justify-center flex-1">
                        <span class="text-xs sm:text-sm font-semibold text-gray-700 group-hover:text-indigo-600 text-center line-clamp-2 leading-tight">
                            {{ $category->nama_kategori }}
                        </span>
                    </div>
                
                </a>
            @endforeach

        </div>
    </div>
@endsection