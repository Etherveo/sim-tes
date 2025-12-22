@extends('layouts.admin')

@section('content')
    
    <div x-data="{ 
        openStatusModal: false, 
        targetUrl: '', 
        targetStatus: '', 
        targetStatusLabel: '',
        
        confirmStatus(url, status, label) {
            this.targetUrl = url;
            this.targetStatus = status;
            this.targetStatusLabel = label;
            this.openStatusModal = true;
        }
    }">

        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
            <h3 class="text-3xl font-medium text-gray-700">Daftar Pesanan Masuk</h3>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded relative shadow-sm">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
            <form action="{{ route('admin.orders.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                
                <div class="flex-1 relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Cari ID Pesanan atau Nama Pelanggan..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="w-full md:w-48">
                    <select name="filter_status" class="w-full border border-gray-300 rounded-md py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500 text-gray-700" onchange="this.form.submit()">
                        <option value="all">Semua Status</option>
                        <option value="pending" {{ request('filter_status') == 'pending' ? 'selected' : '' }}>Sedang Disiapkan</option>
                        <option value="ready" {{ request('filter_status') == 'ready' ? 'selected' : '' }}>Siap Diambil</option>
                        <option value="completed" {{ request('filter_status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>

                <div class="w-full md:w-56">
                    <select name="sort_by" class="w-full border border-gray-300 rounded-md py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500 text-gray-700" onchange="this.form.submit()">
                        <option value="default" {{ request('sort_by') == 'default' ? 'selected' : '' }}>Urutan Default (Baru)</option>
                        <option value="pickup_closest" {{ request('sort_by') == 'pickup_closest' ? 'selected' : '' }}>Jadwal Ambil Terdekat</option>
                    </select>
                </div>

                @if(request('search') || request('filter_status') || request('sort_by'))
                    <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-md hover:bg-gray-200 flex items-center justify-center">
                        Reset
                    </a>
                @endif

            </form>
        </div>

        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID / Pelanggan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jadwal Pengambilan</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jml Barang</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Harga</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lihat Detail Pesanan</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Perbarui Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($orders as $order)
                                    <tr class="{{ $order->status == 'completed' ? 'bg-gray-50' : '' }}"> 
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-900">#{{ $order->id }}</div>
                                            <div class="text-sm text-gray-600">{{ $order->user->nama_lengkap ?? 'User Terhapus' }}</div>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($order->pickup_date)
                                                <div class="text-sm font-semibold text-indigo-700">
                                                    {{ \Carbon\Carbon::parse($order->pickup_date)->format('d M Y') }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    Pukul {{ \Carbon\Carbon::parse($order->pickup_time)->format('H:i') }}
                                                </div>
                                            @else
                                                <span class="text-sm text-gray-400">-</span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $order->items->sum('quantity') }} pcs
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($order->status == 'pending')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Sedang Disiapkan
                                                </span>
                                            @elseif($order->status == 'ready')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    Siap Diambil
                                                </span>
                                            @elseif($order->status == 'completed')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Selesai
                                                </span>
                                            @elseif($order->status == 'cancelled')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Dibatalkan (Otomatis)
                                            </span>    
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-medium space-x-2">
                                            
                                            <a href="{{ route('admin.orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-900 font-bold mr-2">
                                                Lihat Detail
                                            </a>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">

                                            @if($order->status == 'pending')
                                                <button @click="confirmStatus('{{ route('admin.orders.updateStatus', $order) }}', 'ready', 'Siap Diambil')" 
                                                        class="bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1 rounded-md transition duration-150">
                                                    Set Siap
                                                </button>
                                            
                                            @elseif($order->status == 'ready')
                                                <button @click="confirmStatus('{{ route('admin.orders.updateStatus', $order) }}', 'completed', 'Selesai')" 
                                                        class="bg-green-50 text-green-600 hover:bg-green-100 px-3 py-1 rounded-md transition duration-150">
                                                    Set Selesai
                                                </button>
                                            
                                            @elseif($order->status == 'completed')
                                                <span class="text-green-500 cursor-default inline-flex items-center">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                </span>

                                            @elseif($order->status == 'cancelled')
                                                <span class="text-red-500 cursor-default text-xs">
                                                    Batal
                                                </span>
                                            @endif

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                            @if(request('search') || request('filter_status'))
                                                Tidak ada pesanan yang sesuai dengan filter.
                                            @else
                                                Belum ada pesanan masuk.
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

        <div x-show="openStatusModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto" x-cloak>
            <div x-show="openStatusModal" class="fixed inset-0 bg-black/50" @click="openStatusModal = false"></div>
            <div x-show="openStatusModal" class="relative w-full max-w-md p-6 bg-white rounded-lg shadow-lg m-4">
                <div class="flex items-center mb-4 text-yellow-600">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <h3 class="text-lg font-bold text-gray-900">Konfirmasi Perubahan Status</h3>
                </div>
                <p class="text-gray-600 mb-6">Apakah Anda yakin ingin memperbarui status pesanan ini menjadi <span class="font-bold text-indigo-600" x-text="targetStatusLabel"></span>?</p>
                <div class="flex justify-end space-x-3">
                    <button @click="openStatusModal = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition">Batal</button>
                    <form :action="targetUrl" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" :value="targetStatus">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition shadow-md">Ya, Perbarui</button>
                    </form>
                </div>
            </div>
        </div>

    </div>

@endsection