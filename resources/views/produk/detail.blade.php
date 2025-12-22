@extends('layouts.app')

@section('content')
<div x-data="{}" class="container mx-auto max-w-7xl py-12 px-4 sm:px-6 lg:px-8">
    
    <a href="{{ url()->previous() }}" class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-indigo-600 mb-6">
        <svg class="h-5 w-5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Kembali
    </a>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 bg-white shadow-lg rounded-lg overflow-hidden p-6 md:p-8">
        
        <div class="w-full flex items-center justify-center p-4 border rounded-lg border-gray-200">
            <img src="{{ asset('storage/'. $product->gambar) }}" alt="{{ $product->nama_produk }}" 
                 class="h-auto w-full max-h-96 object-contain">
        </div>

        <div>
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900">{{ $product->nama_produk }}</h1>
            
            <a href="{{ route('produk.by.kategori', ['kategori_slug' => \Illuminate\Support\Str::slug($product->kategori_produk)]) }}" 
               class="inline-block text-sm font-medium text-indigo-600 uppercase mt-2 hover:text-indigo-800 hover:underline">
                {{ $product->kategori_produk }}
            </a>
            
            <div class="mt-4">
                @if ($product->harga_diskon && $product->harga_diskon < $product->harga)
                    <span class="text-3xl font-bold text-red-600">Rp {{ number_format($product->harga_diskon, 0, ',', '.') }}</span>
                    <span class="ml-3 text-xl text-gray-500 line-through">Rp {{ number_format($product->harga, 0, ',', '.') }}</span>
                @else
                    <span class="text-3xl font-bold text-gray-900">Rp {{ number_format($product->harga, 0, ',', '.') }}</span>
                @endif
            </div>

            <div class="mt-6">
                <span class="text-base font-medium text-gray-700">Stok Tersedia: </span>
                <span class="text-base font-bold text-gray-900">{{ $product->stok }} pcs</span>
            </div>

            <hr class="my-6 border-gray-200">
            
            <div class="mt-6">
                @guest
                    <a href="{{ route('login') }}" class="w-full sm:w-auto flex justify-center py-3 px-6 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-[#ec9837] hover:bg-[#d4872d] transition-colors duration-300">
                        Login untuk Booking
                    </a>
                @endguest
    
                @auth
                    @if(Auth::user()->role == 'pengguna')
                        
                        @if(Auth::user()->no_telepon_verified_at == null)
                            <div class="flex flex-col items-start gap-2">
                                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 w-full mb-2">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-yellow-700">
                                                Anda harus memverifikasi nomor WhatsApp sebelum memesan.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <a href="{{ route('otp.show') }}" 
                                   class="w-full sm:w-auto flex justify-center py-3 px-6 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-yellow-500 hover:bg-yellow-600 transition-colors duration-300">
                                    Verifikasi Sekarang
                                </a>
                            </div>

                        @else
                            @if($product->stok < 1)
                                <button disabled class="w-full sm:w-auto flex justify-center py-3 px-6 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-gray-400 cursor-not-allowed">
                                    Stok Habis
                                </button>
                            @else
                                <button x-show="!$store.cart.items[{{ $product->id }}]" 
                                        @click="$store.cart.addItem({{ $product->id }})"
                                        class="w-full sm:w-auto flex justify-center py-3 px-6 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition-colors duration-300">
                                    Booking
                                </button>
            
                                <div x-show="$store.cart.items[{{ $product->id }}]" x-cloak class="flex items-center space-x-4">
                                    <button @click="$store.cart.decrementItem({{ $product->id }})"
                                            class="p-2 w-12 h-12 rounded-md bg-gray-200 hover:bg-gray-300 text-xl font-bold">
                                        -
                                    </button>
                                    <span class="px-4 text-xl font-semibold" x-text="$store.cart.items[{{ $product->id }}]"></span>
                                    <button @click="$store.cart.incrementItem({{ $product->id }})"
                                            class="p-2 w-12 h-12 rounded-md bg-gray-200 hover:bg-gray-300 text-xl font-bold">
                                        +
                                    </button>
                                </div>
                            @endif
                        @endif

                    @endif
                @endauth
            </div>

            <hr class="my-6 border-gray-200">

            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Deskripsi Produk</h3>
                <p class="text-gray-600 whitespace-pre-wrap">{{ $product->deskripsi }}</p>
            </div>
            
        </div>
    </div>
</div>
@endsection