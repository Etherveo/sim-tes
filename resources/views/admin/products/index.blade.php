@extends('layouts.admin')

@section('content')
    
    <div x-data="{ openDeleteModal: false, deleteFormUrl: '' }">

        <div>
            <h3 class="text-3xl font-medium text-gray-700">Daftar Produk</h3>
        </div>

        @if (session('success'))
            <div class="mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        
        @if (session('error'))
            <div class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <div class="mt-8 flex flex-col sm:flex-row justify-between items-center gap-4">
            
            <div class="flex w-full sm:w-auto gap-2">

                <div x-data="{ 
                    openSort: false, 
                    openFilter: false, 
                    showModal: false, 
                    modalType: '', 
                    catSearch: '' 
                    }" class="mt-8 flex flex-col sm:flex-row justify-between items-center gap-4">
                    
                    <div class="flex w-full sm:w-auto gap-2">
                        <form action="{{ route('admin.dashboard') }}" method="GET" class="relative w-full sm:w-64">
                            @foreach(request()->except(['search', '_token']) as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <div class="flex">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                </span>
                                <input type="text" name="search" value="{{ request('search') }}" class="w-full py-2 pl-10 pr-4 text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500" placeholder="Cari produk...">
                            </div>
                        </form>

                        <div x-data="{ openSort: false }" class="relative">
                            <button @click="openSort = !openSort" @click.away="openSort = false" class="flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none shadow-sm md:w-56">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                                </svg>
                                Urutkan
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>

                            <div x-show="openSort" 
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute z-50 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 left-0 sm:left-auto">
                                
                                <div role="menu" aria-orientation="vertical">
                                    @php
                                        function sortUrl($key, $direction) {
                                            return request()->fullUrlWithQuery(['sort_by' => $key, 'sort_direction' => $direction]);
                                        }
                                        $currentSort = request('sort_by', 'nama');
                                        $currentDir = request('sort_direction', 'asc');
                                    @endphp

                                    <div class="px-4 py-2 rounded text-xs font-semibold text-gray-500 uppercase tracking-wider bg-yellow-400">Stok</div>
                                    <a href="{{ sortUrl('stok', 'desc') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 {{ $currentSort == 'stok' && $currentDir == 'desc' ? 'bg-indigo-50 text-indigo-700 font-bold' : '' }}">Stok Terbanyak</a>
                                    <a href="{{ sortUrl('stok', 'asc') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 {{ $currentSort == 'stok' && $currentDir == 'asc' ? 'bg-indigo-50 text-indigo-700 font-bold' : '' }}">Stok Sedikit</a>

                                    <div class="border-t border-gray-100 my-1"></div>
                                    <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider bg-yellow-400">Nama</div>
                                    <a href="{{ sortUrl('nama', 'asc') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 {{ $currentSort == 'nama' && $currentDir == 'asc' ? 'bg-indigo-50 text-indigo-700 font-bold' : '' }}">Nama (A-Z)</a>
                                    <a href="{{ sortUrl('nama', 'desc') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 {{ $currentSort == 'nama' && $currentDir == 'desc' ? 'bg-indigo-50 text-indigo-700 font-bold' : '' }}">Nama (Z-A)</a>

                                    <div class="border-t border-gray-100 my-1"></div>
                                    <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider bg-yellow-400">Harga</div>
                                    <a href="{{ sortUrl('harga', 'asc') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 {{ $currentSort == 'harga' && $currentDir == 'asc' ? 'bg-indigo-50 text-indigo-700 font-bold' : '' }}">Termurah</a>
                                    <a href="{{ sortUrl('harga', 'desc') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 {{ $currentSort == 'harga' && $currentDir == 'desc' ? 'bg-indigo-50 text-indigo-700 font-bold' : '' }}">Termahal</a>

                                    <div class="border-t border-gray-100 my-1"></div>
                                    <a href="{{ sortUrl('kategori', 'asc') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 {{ $currentSort == 'kategori' ? 'bg-indigo-50 text-indigo-700 font-bold' : '' }}">Kategori (A-Z)</a>

                                    <div class="border-t border-gray-100 my-1"></div>
                                    <a href="{{ sortUrl('kode', 'desc') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 {{ $currentSort == 'kode' && $currentDir == 'desc' ? 'bg-indigo-50 text-indigo-700 font-bold' : '' }}">Terbaru (ID)</a>
                                </div>
                            </div>
                        </div>

                        <div class="relative">
                            <button @click="openFilter = !openFilter" @click.away="openFilter = false" 
                                    class="flex justify-center items-center md:w-56 px-4 py-2 bg-white border {{ request()->hasAny(['filter_kategori', 'filter_diskon', 'filter_stok_kosong']) ? 'border-indigo-500 text-indigo-600 bg-indigo-50' : 'border-gray-300 text-gray-700' }} rounded-lg shadow-sm">
                                Filter
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>

                            <div x-show="openFilter" style="display: none;" class="absolute z-40 mt-2 w-58 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 right-0">
                                <div class="py-1">
                                    <button @click="modalType = 'kategori'; showModal = true; openFilter = false" class="w-full text-center px-4 py-2 text-xl text-gray-700 hover:bg-gray-100 {{ request('filter_kategori') ? 'bg-indigo-100' : '' }}">
                                        <span>Kategori</span>
                                        <!-- <span class="text-xs text-indigo-500">{{ request('filter_kategori') ? '●' : '' }}</span> -->
                                    </button>
                                    <button @click="modalType = 'harga'; showModal = true; openFilter = false" class="w-full text-center px-4 py-2 text-xl text-gray-700 hover:bg-gray-100 {{ request('filter_harga_min') ? 'bg-indigo-100' : '' }}">
                                        <span>Harga</span>
                                        <!-- <span class="text-xs text-indigo-500">{{ request('filter_harga_min') ? '●' : '' }}</span> -->
                                    </button>
                                    <button @click="modalType = 'stok'; showModal = true; openFilter = false" class="w-full text-center px-4 py-2 text-xl text-gray-700 hover:bg-gray-100 {{ request('filter_stok_min') || request('filter_stok_kosong') ? 'bg-indigo-100' : '' }}">
                                        <span>Stok</span>
                                        <span class="text-xs text-indigo-500">{{ request('filter_stok_min') || request('filter_stok_kosong') ? '●' : '' }}</span>
                                    </button>
                                    <div class="border-t my-1"></div>
                                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-xs text-red-500 hover:bg-red-50 text-center">Reset Filter</a>
                                </div>
                            </div>
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
                                
                                <form action="{{ route('admin.dashboard') }}" method="GET">
                                    @foreach(request()->except(['filter_kategori', 'filter_diskon', 'filter_harga_min', 'filter_harga_max', 'filter_stok_kosong', 'filter_stok_min', 'filter_stok_max', '_token']) as $key => $value)
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
                                            <label class="flex items-center p-4 border-2 border-dashed border-indigo-200 rounded-lg cursor-pointer bg-indigo-50 hover:bg-indigo-100 transition">
                                                <input type="checkbox" name="filter_diskon" value="1" 
                                                    {{ request('filter_diskon') == '1' ? 'checked' : '' }} 
                                                    class="h-5 w-5 rounded text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                                <div class="ml-3">
                                                    <span class="block font-semibold text-indigo-700">Tampilkan Produk Sedang Diskon Saja</span>
                                                </div>
                                            </label>
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

                                        <div x-show="modalType === 'stok'" class="space-y-6">
                                            <label class="flex items-center p-4 border-2 border-dashed border-red-200 rounded-lg cursor-pointer bg-red-50 hover:bg-red-100 transition">
                                                <input type="checkbox" name="filter_stok_kosong" value="1" {{ request('filter_stok_kosong') ? 'checked' : '' }} class="h-5 w-5 rounded text-red-600 border-gray-300 focus:ring-red-500">
                                                <span class="ml-3 font-semibold text-red-700">Tampilkan Produk Stok Habis (0)</span>
                                            </label>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div>
                                                    <label class="block text-sm font-bold text-gray-700 mb-2">Stok Minimum</label>
                                                    <input type="number" name="filter_stok_min" value="{{ request('filter_stok_min') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="0">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-bold text-gray-700 mb-2">Stok Maksimum</label>
                                                    <input type="number" name="filter_stok_max" value="{{ request('filter_stok_max') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Limit stok">
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

            </div>

            <div class="w-full sm:w-auto mt-4 sm:mt-0">
                <a href="{{ route('admin.products.create') }}" class="px-4 py-2 bg-yellow-400 hover:bg-indigo-700 text-white rounded-lg flex items-center justify-center">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Produk
                </a>
            </div>
        </div>

        <div class="flex flex-col mt-6">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8 hide-scrollbar">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gambar</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                
                                @forelse ($products as $product)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $product->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($product->gambar)
                                            <img src="{{ asset('storage/' . $product->gambar) }}" alt="{{ $product->nama_produk }}" class="h-10 w-10 rounded-md object-cover">
                                        @else
                                            <span class="h-10 w-10 rounded-md bg-gray-200 flex items-center justify-center text-xs">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $product->nama_produk }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $product->kategori_produk }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if ($product->harga_diskon && $product->harga_diskon < $product->harga)
                                            <span class="font-bold text-red-600">Rp {{ number_format($product->harga_diskon, 0, ',', '.') }}</span>
                                            <span class="block text-xs text-gray-500 line-through">Rp {{ number_format($product->harga, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-gray-900">Rp {{ number_format($product->harga, 0, ',', '.') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $product->stok }} pcs
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <a href="{{ route('admin.products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        
                                        <button type="button" 
                                                @click="deleteFormUrl = '{{ route('admin.products.destroy', $product) }}'; openDeleteModal = true"
                                                class="text-red-600 hover:text-red-900">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        @if (request('search'))
                                            Tidak ada produk yang ditemukan untuk pencarian "{{ request('search') }}".
                                        @else
                                            Belum ada produk yang ditambahkan.
                                        @endif
                                    </td>
                                </tr>
                                @endforelse

                            </tbody>
                        </table>
                        <div class="mt-6">
                            {{ $products->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="openDeleteModal" 
             class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto" 
             x-cloak>
            
            <div x-show="openDeleteModal" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/50"
                 @click="openDeleteModal = false"></div>

            <div x-show="openDeleteModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-90"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-90"
                 class="relative w-full max-w-md p-6 bg-white rounded-lg shadow-lg">
                
                <form :action="deleteFormUrl" method="POST" class="text-center">
                    @csrf
                    @method('DELETE')

                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>

                    <h3 class="mt-4 text-lg font-medium text-gray-900">
                        Hapus Produk
                    </h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Apakah Anda yakin ingin menghapus produk ini? Tindakan ini tidak dapat dibatalkan.
                    </p>

                    <div class="mt-6 flex justify-center space-x-4">
                        <button type="button" 
                                @click="openDeleteModal = false"
                                class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Yakin, Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </div> @endsection