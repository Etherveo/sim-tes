@extends('layouts.app')

@section('content')
    <div class="w-full mx-auto py-12 px-4 sm:px-6 lg:px-16 xl:px-24">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Daftar Kategori</h1>
        <form action="{{ route('kategori.index') }}" method="GET" class="relative w-full sm:w-64 mb-10">
                @foreach(request()->except(['search', '_token']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <div class="flex">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" class="w-full py-2 pl-10 pr-4 text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500" placeholder="Cari Kategori...">
                </div>
        </form>
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

            <div class="grid grid-cols-2 gap-4 md:gap-6 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 mt-12">
                @forelse ($categories as $category)
                @empty
                    <div class="col-span-full py-20 text-center">
                        <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-4 text-lg font-bold text-gray-900">Kategori "{{ request('search') }}" Tidak Ditemukan</h3>
                        <p class="mt-2 text-gray-500">
                            Coba gunakan kata kunci lain.
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('kategori.index') }}" class="text-indigo-600 font-semibold hover:text-indigo-800">
                                &larr; Kembali ke Semua Kategori
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection