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
                
                <form action="{{ route('admin.dashboard') }}" method="GET" class="relative w-full sm:w-80">
                    @if(request('sort_by'))
                        <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                    @endif
                    @if(request('sort_direction'))
                        <input type="hidden" name="sort_direction" value="{{ request('sort_direction') }}">
                    @endif

                    <div class="flex">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               class="w-full py-2 pl-10 pr-4 text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500" 
                               placeholder="Cari produk...">
                    </div>
                </form>

                <div x-data="{ openSort: false }" class="relative">
                    <button @click="openSort = !openSort" @click.away="openSort = false" class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none shadow-sm">
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
                        
                        <div class="py-1" role="menu" aria-orientation="vertical">
                            @php
                                function sortUrl($key, $direction) {
                                    return request()->fullUrlWithQuery(['sort_by' => $key, 'sort_direction' => $direction]);
                                }
                                $currentSort = request('sort_by', 'nama');
                                $currentDir = request('sort_direction', 'asc');
                            @endphp

                            <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider bg-yellow-400">Stok</div>
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
            </div>

            <div class="w-full sm:w-auto mt-4 sm:mt-0">
                <a href="{{ route('admin.products.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg flex items-center justify-center">
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