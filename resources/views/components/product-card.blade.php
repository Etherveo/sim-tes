@props(['product'])

<div x-data="{}" class="bg-white rounded-lg shadow-lg overflow-hidden flex flex-col justify-between transform hover:scale-105 transition-transform duration-300 h-full">
    <div>

        <a href="{{ route('produk.detail', $product) }}" class="block h-32 sm:h-56 w-full bg-white flex items-center justify-center p-2">
            <img src="{{ asset('storage/' . $product->gambar) }}" alt="{{ $product->nama_produk }}" class="h-full w-full object-contain">
        </a>

        <div class="p-3 sm:p-6">
            <a href="{{ route('produk.detail', $product) }}">
                
                <h3 class="text-sm sm:text-lg font-semibold text-gray-900 hover:text-indigo-700 line-clamp-2 leading-tight min-h-[2.5em]">
                    {{ $product->nama_produk }}
                </h3>
            </a>
            
            <p class="mt-1 text-[10px] sm:text-xs font-medium text-indigo-600 uppercase truncate">
                {{ $product->kategori_produk }}
            </p>
            <p class="mt-1 sm:mt-2 text-xs sm:text-sm text-gray-600 line-clamp-1 sm:line-clamp-2">
                {{ $product->deskripsi }}
            </p>
        </div>
    </div>

    <div class="p-3 sm:p-6 bg-gray-50 border-t border-gray-200">
        <div class="mb-2 sm:mb-4">
            @if ($product->harga_diskon && $product->harga_diskon < $product->harga)
                <div class="flex flex-col sm:flex-row sm:items-center sm:flex-wrap">
                    <span class="text-base sm:text-xl font-bold text-red-600">Rp {{ number_format($product->harga_diskon, 0, ',', '.') }}</span>
                    <span class="text-[10px] sm:text-sm text-gray-500 line-through sm:ml-2">Rp {{ number_format($product->harga, 0, ',', '.') }}</span>
                </div>
            @else
                <span class="text-base sm:text-xl font-bold text-gray-900">Rp {{ number_format($product->harga, 0, ',', '.') }}</span>
            @endif
        </div>
        
        <span class="text-[10px] sm:text-sm font-medium text-gray-500 mb-3 sm:mb-4 block">Stok: {{ $product->stok }} pcs</span>

        <div class="mt-auto">
            @guest
                <a href="{{ route('login') }}" 
                   class="w-full flex justify-center py-1.5 sm:py-2 px-2 sm:px-4 border border-transparent rounded-md shadow-sm text-xs sm:text-sm font-medium text-white bg-[#faa918] hover:bg-[#dfa528]">
                    Tambahkan Ke Keranjang
                </a>
            @endguest

            @auth
                @if(Auth::user()->role == 'pengguna')
                    
                    @if(Auth::user()->no_telepon_verified_at == null)
                        <a href="{{ route('otp.show') }}" 
                           class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-500 hover:bg-yellow-600 text-center">
                            Verifikasi WA Terlebih Dahulu Sebelum memesan
                        </a>

                    @else
                        @if($product->stok < 1)
                             <button disabled class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-gray-400 bg-gray-200 cursor-not-allowed">
                                Habis
                            </button>
                        @else
                            <button x-show="!$store.cart.items[{{ $product->id }}]" 
                                    @click="$store.cart.addItem({{ $product->id }})"
                                    class="w-full flex justify-center py-1.5 sm:py-2 px-4 border border-transparent rounded-md shadow-sm text-xs sm:text-sm font-medium text-white bg-yellow-500 hover:bg-yellow-600">
                                Tambahkan Ke Keranjang
                            </button>

                            <div x-show="$store.cart.items[{{ $product->id }}]" x-cloak>
                                <div class="flex items-center justify-between gap-2">
                                    <button @click="$store.cart.decrementItem({{ $product->id }})" class="p-1 sm:p-2 w-8 h-8 sm:w-10 sm:h-10 rounded-md bg-gray-200 hover:bg-gray-300 text-base sm:text-lg font-bold flex items-center justify-center">-</button>
                                    <span class="text-sm sm:text-lg font-semibold" x-text="$store.cart.items[{{ $product->id }}]"></span>
                                    <button @click="if($store.cart.items[{{ $product->id }}] < {{ $product->stok }}) { $store.cart.incrementItem({{ $product->id }}) }" 
                                            :disabled="$store.cart.items[{{ $product->id }}] >= {{ $product->stok }}"
                                            :class="$store.cart.items[{{ $product->id }}] >= {{ $product->stok }} ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-gray-200 hover:bg-gray-300 text-gray-900'"
                                            class="p-1 sm:p-2 w-8 h-8 sm:w-10 sm:h-10 rounded-md text-base sm:text-lg font-bold flex items-center justify-center">+</button>
                                </div>
                                <div x-show="$store.cart.items[{{ $product->id }}] >= {{ $product->stok }}" class="text-center mt-1"><span class="text-[10px] text-red-500 font-medium">Max stok!</span></div>
                            </div>
                        @endif
                    @endif

                @endif
            @endauth
        </div>
    </div>
</div>