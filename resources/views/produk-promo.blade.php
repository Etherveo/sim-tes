@extends('layouts.app')

@section('content')
    <div class="container mx-auto max-w-7xl py-5 px-4 sm:px-6 lg:px-8">

    <h1 class="text-3xl font-bold text-gray-900 mb-8">
        Semua Produk Berdiskon!
    </h1>

        <div x-data="{ 
            openSort: false, 
            openFilter: false, 
            showModal: false, 
            modalType: '', 
            catSearch: '' 
            }" class="relative inline-flex text-left mb-10">

            <button @click="openSort = !openSort" @click.away="openSort = false" class="inline-flex justify-between items-center w-full md:w-48 px-4 py-2 bg-white border border-gray-200 rounded-full text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none shadow-sm transition">
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/></svg>
                    Urutkan
                </span>
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <button @click="openFilter = !openFilter" @click.away="openFilter = false" 
                class="flex items-center px-4 py-2 bg-white border {{ request()->hasAny(['filter_kategori', 'filter_diskon']) ? 'border-indigo-500 text-indigo-600 bg-indigo-50' : 'border-gray-300 text-gray-700' }} rounded-lg shadow-sm">
                Filter
            </button>

            <div x-show="openFilter" style="display: none;" class="absolute z-40 mt-10 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 right-0">
                <div class="py-1">
                    <button @click="modalType = 'kategori'; showModal = true; openFilter = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex justify-between">
                        <span>Kategori</span>
                        <span class="text-sm text-indigo-500">{{ request('filter_kategori') ? '●' : '' }}</span>
                    </button>
                    <button @click="modalType = 'harga'; showModal = true; openFilter = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex justify-between">
                        <span>Harga</span>
                        <span class="text-sm text-indigo-500">{{ request('filter_harga_min') ? '●' : '' }}</span>
                    </button>
                    <div class="border-t my-1"></div>
                    <a href="{{ route('produk.promo') }}" class="block px-4 py-2 text-sm text-red-500 hover:bg-red-50">Reset Filter</a>
                </div>
            </div>

            <div x-show="openSort" style="display: none;" class="absolute left-0 z-30 mt-10 w-56 rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 overflow-hidden">
                <div class="py-1">
                    @php $sUrl = fn($k, $d) => request()->fullUrlWithQuery(['sort_by' => $k, 'sort_direction' => $d]); @endphp
                    <a href="{{ $sUrl('nama', 'asc') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50">Nama (A-Z)</a>
                    <a href="{{ $sUrl('nama', 'desc') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50">Nama (Z-A)</a>
                </div>
                <div class="py-1">
                    <a href="{{ $sUrl('harga', 'asc') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50">Harga Termurah</a>
                    <a href="{{ $sUrl('harga', 'desc') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50">Harga Termahal</a>
                </div>
                <div class="py-1">
                    <a href="{{ $sUrl('kode', 'desc') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50">Produk Terbaru (Default)</a>
                </div>
            </div>

            <div x-show="showModal" 
                class="fixed inset-0 z-[60] overflow-y-auto" 
                style="display: none;"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">

                <div class="fixed inset-0 bg-black/50 bg-opacity-10 transition-opacity"></div>
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">

                    <div @click.away="showModal = false" 
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 w-[90%] md:w-[80%] lg:w-[70%] max-w-5xl">  

                        <form action="{{ route('produk.promo') }}" method="GET">
                            @foreach(request()->except(['filter_kategori', 'filter_diskon', 'filter_harga_min', 'filter_harga_max', '_token']) as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach

                            <div class="bg-white px-6 py-6">
                                <div class="flex justify-between items-center border-b pb-3 mb-4">
                                    <h3 class="text-lg font-bold text-gray-900 uppercase tracking-wider" x-text="'Filter ' + modalType"></h3>
                                    <button type="button" @click="showModal = false" class="text-gray-400 hover:text-gray-500">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>

                                <div x-show="modalType === 'kategori'">
                                    <div class="mb-4">
                                        <input x-model="catSearch" type="text" placeholder="Cari kategori..." class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 max-h-[50vh] overflow-y-auto p-1">
                                        <label class="flex items-center p-3 border rounded-md hover:bg-gray-50 cursor-pointer">
                                            <input type="radio" name="filter_kategori" value="" {{ request('filter_kategori') == '' ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                            <span class="ml-3 text-sm font-medium text-gray-700 italic">Semua Kategori</span>
                                        </label>
                                        @foreach($categories as $cat)
                                            <label x-show="'{{ strtolower($cat->nama_kategori) }}'.includes(catSearch.toLowerCase())" 
                                                class="flex items-center p-3 border rounded-md hover:bg-gray-50 cursor-pointer">
                                                <input type="radio" name="filter_kategori" value="{{ $cat->nama_kategori }}" {{ request('filter_kategori') == $cat->nama_kategori ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                                <span class="ml-3 text-sm text-gray-700">{{ $cat->nama_kategori }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <div x-show="modalType === 'harga'" class="space-y-6">
                                    <!-- <label class="flex items-center p-4 border-2 border-dashed border-indigo-200 rounded-lg cursor-pointer bg-indigo-50 hover:bg-indigo-100 transition">
                                        <input type="checkbox" name="filter_diskon" value="1" 
                                            {{ request('filter_diskon') == '1' ? 'checked' : '' }} 
                                            class="h-5 w-5 rounded text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                        <div class="ml-3">
                                            <span class="block font-semibold text-indigo-700">Tampilkan Produk Sedang Diskon Saja</span>
                                        </div>
                                    </label> -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">Harga Minimum (Rp)</label>
                                            <input type="number" name="filter_harga_min" value="{{ request('filter_harga_min') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Contoh: 10000">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">Harga Maksimum (Rp)</label>
                                            <input type="number" name="filter_harga_max" value="{{ request('filter_harga_max') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Kosongkan jika tidak ada batas">
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-2">
                                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-6 py-2 text-sm font-bold text-white shadow-sm hover:bg-indigo-700 focus:outline-none">Terapkan Filter</button>
                                <button type="button" @click="showModal = false" class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-6 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4 md:gap-6 md:grid-cols-3 lg:grid-cols-4">
            
            @forelse ($promoProducts as $product)
                <x-product-card :product="$product" />
            @empty
                <p class="col-span-full text-center text-gray-500 text-lg">
                    Maaf, tidak ada promo untuk saat ini.
                </p>
            @endforelse

        </div>

        <div class="mt-12">
            {{ $promoProducts->appends(request()->query())->links() }}
        </div>
    </div>
@endsection