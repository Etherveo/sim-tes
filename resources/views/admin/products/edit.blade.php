@extends('layouts.admin')

@section('content')
    
    <h3 class="text-3xl font-medium text-gray-700">Edit Produk: {{ $product->nama_produk }}</h3>

    @if ($errors->any())
        <div class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <p class="font-bold">Terjadi kesalahan:</p>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-8">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data"
              x-data="{ 
                  harga: '{{ old('harga', $product->harga) }}', 
                  harga_diskon: '{{ old('harga_diskon', $product->harga_diskon) }}',
                  // Logika validasi awal
                  isHargaDiskonError: ( ('{{ old('harga_diskon', $product->harga_diskon) }}' !== null && '{{ old('harga_diskon', $product->harga_diskon) }}' !== '') && 
                                        ('{{ old('harga', $product->harga) }}' !== null && '{{ old('harga', $product->harga) }}' !== '') &&
                                        (parseFloat('{{ old('harga_diskon', $product->harga_diskon) }}') >= parseFloat('{{ old('harga', $product->harga) }}')) ),

                  kategori_open: false, 
                  kategori_selected: '{{ old('kategori_produk', $product->kategori_produk) }}',
                  // Mengambil data kategori dari database
                  kategoriList: {{ Js::from($categories->pluck('nama_kategori')) }},
                  
                  validateHarga() {
                      if (this.harga_diskon !== null && this.harga_diskon !== '' && this.harga !== null && this.harga !== '') {
                          this.isHargaDiskonError = (parseFloat(this.harga_diskon) >= parseFloat(this.harga));
                      } else {
                          this.isHargaDiskonError = false;
                      }
                  }
              }">
            @csrf
            @method('PUT') 
            
            <div class="p-6 bg-white rounded-lg shadow-md">
                
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="gambar">Gambar Produk (Opsional)</label>
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $product->gambar) }}" alt="{{ $product->nama_produk }}" class="h-24 w-auto rounded-md object-cover">
                    </div>
                    <input type="file" name="gambar" id="gambar" class="w-full px-3 py-2 border rounded-md">
                </div>
                
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="nama_produk">Nama Produk</label>
                    <input type="text" name="nama_produk" id="nama_produk" value="{{ old('nama_produk', $product->nama_produk) }}" class="w-full px-3 py-2 border rounded-md" required>
                </div>

                <!-- Dropdown Kategori -->
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Kategori Produk</label>
                    <input type="hidden" name="kategori_produk" x-model="kategori_selected" required>
                    <div class="relative">
                        <button type="button" @click="kategori_open = !kategori_open" class="w-full px-3 py-2 border rounded-md text-gray-700 bg-white text-left flex justify-between items-center focus:outline-none focus:ring">
                            <span x-text="kategori_selected || 'Pilih salah satu kategori...'"></span>
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </button>
                        <div x-show="kategori_open" @click.away="kategori_open = false" class="absolute z-10 w-full mt-1 bg-white rounded-md shadow-lg max-h-60 overflow-y-auto border" x-cloak>
                            <template x-for="kategori in kategoriList" :key="kategori">
                                <div @click="kategori_selected = kategori; kategori_open = false" x-text="kategori" class="px-4 py-2 text-gray-700 cursor-pointer hover:bg-indigo-100" :class="{ 'bg-indigo-100 font-semibold': kategori_selected == kategori }"></div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Harga -->
                <div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="harga">Harga (Rp)</label>
                            <input type="number" name="harga" id="harga" value="{{ old('harga', $product->harga) }}" x-model="harga" @input="validateHarga()" class="w-full px-3 py-2 border rounded-md" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="harga_diskon">Harga Diskon (Opsional)</label>
                            <input type="number" name="harga_diskon" id="harga_diskon" value="{{ old('harga_diskon', $product->harga_diskon) }}" x-model="harga_diskon" @input="validateHarga()" class="w-full px-3 py-2 border rounded-md">
                        </div>
                    </div>
                    <div x-show="isHargaDiskonError" class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg" x-cloak>
                        <span class="font-bold">Peringatan!</span> Harga diskon harus lebih rendah dari harga normal.
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="deskripsi">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" rows="4" class="w-full px-3 py-2 border rounded-md">{{ old('deskripsi', $product->deskripsi) }}</textarea>
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="stok">Stok</label>
                    <input type="number" name="stok" id="stok" value="{{ old('stok', $product->stok) }}" class="w-full px-3 py-2 border rounded-md" required>
                </div>
                
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Batal</a>
                    <button type="submit" :disabled="isHargaDiskonError" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        Simpan Perubahan
                    </button>
                </div>

            </div>
        </form>
    </div>

@endsection