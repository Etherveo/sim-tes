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

        <div class="mt-8 flex flex-col sm:flex-row justify-between">
            <form action="{{ route('admin.dashboard') }}" method="GET" class="relative w-full sm:w-1/2 md:w-1/3">
                <div class="flex">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </span>
                    
                    <input type="text" name="search" placeholder="Cari (Nama, Kategori, Kode)..." 
                           value="{{ request('search') }}"
                           class="w-full pl-10 pr-4 py-2 border rounded-l-md border-gray-400 text-gray-700 bg-white focus:outline-none focus:ring focus:ring-indigo-300">
                    
                    <button type="submit" class="px-4 py-2 bg-[#faa918] text-white rounded-r-md font-medium hover:bg-indigo-700">
                        Cari
                    </button>
                </div>
            </form>
            
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.products.create') }}" 
                   class="flex items-center justify-center px-4 py-2 bg-[#faa918] text-white rounded-md font-medium hover:bg-indigo-700">
                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
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