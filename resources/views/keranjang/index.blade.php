@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-7xl py-12 px-4 sm:px-6 lg:px-8" 
     x-data="{ 
        isLoading: false,
        showCheckoutModal: false,
        showSuccessModal: false, // BARU: Variabel kontrol modal sukses
        
        // Variabel untuk Form Pickup
        pickupDate: '',
        pickupTime: '',
        errorMessage: '',
        successMessage: '', // BARU: Pesan sukses dari server

        calculateTotal() {
            let total = 0;
            const products = {{ Js::from($products) }};
            
            products.forEach(product => {
                if ($store.cart.items[product.id]) {
                    let price = (product.harga_diskon && product.harga_diskon < product.harga) 
                                ? product.harga_diskon 
                                : product.harga;
                    
                    total += price * $store.cart.items[product.id];
                }
            });
            return new Intl.NumberFormat('id-ID').format(total);
        },

        validatePickup() {
            this.errorMessage = '';
            
            if (!this.pickupDate || !this.pickupTime) {
                this.errorMessage = 'Mohon lengkapi tanggal dan jam pengambilan.';
                return false;
            }

            const selectedDate = new Date(this.pickupDate);
            const now = new Date();
            
            const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            const inputDate = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate());

            if (inputDate < today) {
                this.errorMessage = 'Tanggal pengambilan tidak boleh di masa lalu.';
                return false;
            }

            const [hours, minutes] = this.pickupTime.split(':').map(Number);
            const timeValue = hours * 60 + minutes; 
            const minTime = 9 * 60; 
            const maxTime = 20 * 60 + 30; 

            if (timeValue < minTime || timeValue > maxTime) {
                this.errorMessage = 'Jam operasional kami: 09:00 - 20:30.';
                return false;
            }

            if (inputDate.getTime() === today.getTime()) {
                const currentMinutes = now.getHours() * 60 + now.getMinutes();
                if (timeValue < currentMinutes + 30) { 
                    this.errorMessage = 'Waktu pengambilan minimal 30 menit dari sekarang.';
                    return false;
                }
            }

            return true;
        },

        async processCheckout() {
            if (!this.validatePickup()) {
                return; 
            }

            this.isLoading = true;

            try {
                const response = await fetch('{{ route('checkout.process') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        items: $store.cart.items,
                        pickup_date: this.pickupDate,
                        pickup_time: this.pickupTime
                    })
                });

                const result = await response.json();

                if (response.ok) {
                    // MODIFIKASI: Tampilkan Modal Sukses, bukan alert
                    this.showCheckoutModal = false; // Tutup modal checkout
                    this.successMessage = result.message; // Simpan pesan sukses
                    this.showSuccessModal = true; // Buka modal sukses
                    
                    $store.cart.items = {}; 
                    $store.cart.persist();
                    
                    // Kita tidak langsung redirect di sini, tapi menunggu user klik tombol di modal sukses
                } else {
                    alert('Gagal: ' + result.message); // Untuk error, alert masih oke atau bisa buat modal error juga
                    this.isLoading = false;
                }

            } catch (error) {
                console.error(error);
                alert('Terjadi kesalahan jaringan.');
                this.isLoading = false;
            }
        },

        // Fungsi untuk redirect setelah sukses
        finishCheckout() {
            window.location.href = '{{ url('/') }}';
        }
     }">

    <h1 class="text-3xl font-bold text-gray-900 mb-8">Keranjang Belanja</h1>

    <div class="flex flex-col lg:flex-row gap-8">
        
        <div class="w-full lg:w-3/4">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="hidden md:grid grid-cols-12 gap-4 p-4 bg-gray-50 border-b border-gray-200 text-sm font-semibold text-gray-600 uppercase">
                    <div class="col-span-5">Produk</div>
                    <div class="col-span-3 text-center">Harga</div>
                    <div class="col-span-2 text-center">Jumlah</div>
                    <div class="col-span-2 text-center">Total</div>
                </div>

                @foreach($products as $product)
                <div class="p-4 border-b border-gray-200 last:border-b-0"
                     x-show="$store.cart.items[{{ $product->id }}]" x-cloak x-transition>
                    <div class="flex flex-col md:grid md:grid-cols-12 md:gap-4 md:items-center">
                        <div class="flex items-start md:items-center md:col-span-5 w-full mb-4 md:mb-0">
                            <div class="flex-shrink-0 w-20 h-20 border border-gray-200 rounded-md overflow-hidden">
                                <img src="{{ asset('storage/' . $product->gambar) }}" alt="{{ $product->nama_produk }}" class="w-full h-full object-cover">
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-base font-medium text-gray-900 line-clamp-2">{{ $product->nama_produk }}</h3>
                                <p class="text-sm text-gray-500">{{ $product->kategori_produk }}</p>
                                <button @click="$store.cart.removeItem({{ $product->id }})" class="text-red-500 text-sm hover:underline mt-1">Hapus</button>
                            </div>
                        </div>
                        <div class="flex justify-between items-center w-full md:contents">
                            <div class="md:col-span-3 md:text-center">
                                @if ($product->harga_diskon && $product->harga_diskon < $product->harga)
                                    <div class="text-red-600 font-semibold text-sm md:text-base">Rp {{ number_format($product->harga_diskon, 0, ',', '.') }}</div>
                                    <div class="text-xs text-gray-400 line-through">Rp {{ number_format($product->harga, 0, ',', '.') }}</div>
                                @else
                                    <div class="text-gray-900 font-semibold text-sm md:text-base">Rp {{ number_format($product->harga, 0, ',', '.') }}</div>
                                @endif
                            </div>
                            <div class="md:col-span-2 flex flex-col items-center justify-center">
                                <div class="flex items-center border border-gray-200 rounded-md h-9 w-fit mx-auto">
                                    <button @click="$store.cart.decrementItem({{ $product->id }})" class="w-9 h-full flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold transition-colors">-</button>
                                    <span class="w-12 h-full flex items-center justify-center text-sm text-gray-900 font-semibold border-x border-gray-200" x-text="$store.cart.items[{{ $product->id }}]"></span>
                                    <button @click="if($store.cart.items[{{ $product->id }}] < {{ $product->stok }}) { $store.cart.incrementItem({{ $product->id }}) }"
                                            :disabled="$store.cart.items[{{ $product->id }}] >= {{ $product->stok }}"
                                            :class="$store.cart.items[{{ $product->id }}] >= {{ $product->stok }} ? 'bg-gray-50 text-gray-300 cursor-not-allowed' : 'bg-gray-100 hover:bg-gray-200 text-gray-600'"
                                            class="w-9 h-full flex items-center justify-center font-bold transition-colors">+</button>
                                </div>
                                <div x-show="$store.cart.items[{{ $product->id }}] >= {{ $product->stok }}" class="mt-1 text-center"><span class="text-[10px] text-red-500 font-medium leading-tight block">Max stok!</span></div>
                            </div>
                            <div class="hidden md:block md:col-span-2 text-center font-bold text-gray-900">
                                Rp <span x-text="new Intl.NumberFormat('id-ID').format(({{ ($product->harga_diskon && $product->harga_diskon < $product->harga) ? $product->harga_diskon : $product->harga }}) * $store.cart.items[{{ $product->id }}])"></span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                <div x-show="$store.cart.count === 0" x-cloak class="p-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    <h2 class="text-xl font-medium text-gray-900">Keranjang Anda Kosong</h2>
                    <p class="text-gray-500 mt-2">Belum ada produk yang dibooking.</p>
                    <a href="{{ url('/') }}" class="mt-6 inline-block bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700">Mulai Belanja</a>
                </div>
            </div>
        </div>

        <div class="w-full lg:w-1/4" x-show="$store.cart.count > 0" x-cloak>
            <div class="bg-white shadow-lg rounded-lg p-6 sticky top-24">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Ringkasan Pesanan</h2>
                <div class="flex justify-between mb-2 text-gray-600"><span>Total Item</span><span x-text="$store.cart.count"></span></div>
                <div class="border-t border-gray-200 my-4"></div>
                <div class="flex justify-between mb-6">
                    <span class="text-lg font-bold text-gray-900">Total Harga</span>
                    <span class="text-lg font-bold text-[#8058b9]">Rp <span x-text="calculateTotal()"></span></span>
                </div>
                <button @click="showCheckoutModal = true" class="w-full bg-[#ec9837] hover:bg-[#d4872d] text-white font-bold py-3 rounded-md transition duration-300 shadow-md flex justify-center items-center">
                    Checkout Sekarang
                </button>
            </div>
        </div>

    </div>

    <div x-show="showCheckoutModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto" x-cloak>
        <div x-show="showCheckoutModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/50" @click="showCheckoutModal = false"></div>
        
        <div x-show="showCheckoutModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" class="relative w-full max-w-md p-6 bg-white rounded-xl shadow-2xl transform transition-all m-4">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-xl font-bold text-gray-900">Jadwal Pengambilan</h3>
                <button @click="showCheckoutModal = false" class="text-gray-400 hover:text-gray-500"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pengambilan</label>
                    <input type="date" x-model="pickupDate" min="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jam Pengambilan (09:00 - 20:30)</label>
                    <input type="time" x-model="pickupTime" min="09:00" max="20:30" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2 border">
                </div>
                <div class="p-3 bg-blue-50 text-blue-700 text-xs rounded-md"><p>Silakan datang ke toko kami sesuai jadwal yang Anda pilih untuk melakukan pembayaran dan pengambilan barang.</p></div>
                <div x-show="errorMessage" x-text="errorMessage" class="text-red-600 text-sm font-medium bg-red-50 p-2 rounded"></div>
            </div>

            <div class="mt-8 flex justify-end space-x-3">
                <button @click="showCheckoutModal = false" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">Batal</button>
                <button @click="processCheckout()" :disabled="isLoading" :class="isLoading ? 'opacity-50 cursor-not-allowed' : 'hover:bg-[#d4872d]'" class="px-4 py-2 bg-[#ec9837] text-white font-bold rounded-lg shadow-md flex items-center">
                    <span x-show="!isLoading">Konfirmasi Pesanan</span>
                    <span x-show="isLoading" x-cloak class="flex items-center"><svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Memproses...</span>
                </button>
            </div>
        </div>
    </div>

    <div x-show="showSuccessModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto" x-cloak>
        <div x-show="showSuccessModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/50"></div>
        
        <div x-show="showSuccessModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" class="relative w-full max-w-md p-8 bg-white rounded-2xl shadow-2xl transform transition-all m-4 text-center">

            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-green-100 mb-6 animate-bounce">
                <svg class="h-10 w-10 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h3 class="text-2xl font-bold text-gray-900 mb-2">Pesanan Berhasil!</h3>
            
            <p class="text-gray-500 mb-8" x-text="successMessage || 'Terima kasih telah berbelanja. Kami menunggu kedatangan Anda.'"></p>

            <button @click="finishCheckout()" class="w-full bg-[#ec9837] hover:bg-[#d4872d] text-white font-bold py-3 rounded-xl shadow-lg transition duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#ec9837]">
                Kembali ke Beranda
            </button>
        </div>
    </div>

</div>
@endsection