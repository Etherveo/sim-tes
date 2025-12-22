@extends('layouts.admin')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h3 class="text-3xl font-medium text-gray-700">Laporan Penjualan</h3>
            <!-- Gunakan null coalescing operator (??) untuk keamanan -->
            <p class="text-sm text-gray-500 mt-1">{{ $periodLabel ?? 'Periode Laporan' }}</p>
        </div>
        
        <!-- 
          FORM FILTER (Alpine.js) 
          Perbaikan: Pastikan filterType memiliki nilai default jika variabel blade kosong (fallback)
        -->
        <form action="{{ route('admin.reports.index') }}" method="GET" 
              class="flex flex-wrap gap-2 items-center bg-white p-2 rounded-lg shadow-sm border border-gray-200"
              x-data="{ filterType: '{{ $filterType ?? 'monthly' }}' }">
            
            <!-- Hidden Input -->
            <input type="hidden" name="filter_type" x-model="filterType">

            <!-- Switcher Harian/Bulanan -->
            <div class="flex bg-gray-100 rounded p-1 mr-2">
                <button type="button" @click="filterType = 'daily'" 
                        :class="filterType === 'daily' ? 'bg-white text-indigo-600 shadow' : 'text-gray-500 hover:text-gray-700'"
                        class="px-3 py-1 rounded text-sm font-medium transition">
                    Harian
                </button>
                <button type="button" @click="filterType = 'monthly'" 
                        :class="filterType === 'monthly' ? 'bg-white text-indigo-600 shadow' : 'text-gray-500 hover:text-gray-700'"
                        class="px-3 py-1 rounded text-sm font-medium transition">
                    Bulanan
                </button>
            </div>

            <!-- Input Tanggal (Harian) -->
            <div x-show="filterType === 'daily'" x-cloak>
                <input type="date" name="date" value="{{ $date ?? date('Y-m-d') }}" 
                       class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring focus:ring-indigo-200">
            </div>

            <!-- Input Bulan & Tahun (Bulanan) -->
            <div x-show="filterType === 'monthly'" x-cloak class="flex gap-2">
                <select name="month" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring focus:ring-indigo-200">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ ($month ?? now()->month) == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endforeach
                </select>

                <select name="year" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring focus:ring-indigo-200">
                    @foreach(range(date('Y'), 2024) as $y)
                        <option value="{{ $y }}" {{ ($year ?? now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition text-sm font-medium">
                Tampilkan
            </button>
        </form>
    </div>

    <!-- Tombol Export -->
    <div class="flex gap-3 mb-8">
        <a href="{{ route('admin.reports.export.transactions', request()->all()) }}" 
           class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 flex items-center shadow-sm text-sm font-medium transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Export Transaksi (.xlsx)
        </a>
        
        <a href="{{ route('admin.reports.export.products', request()->all()) }}" 
           class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center shadow-sm text-sm font-medium transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Export Produk Terlaris (.xlsx)
        </a>
    </div>

    <!-- Kartu Ringkasan -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Pendapatan -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-indigo-500 flex items-center">
            <div class="p-3 rounded-full bg-indigo-100 text-indigo-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500 font-medium">Pendapatan {{ ($filterType ?? 'monthly') == 'daily' ? 'Harian' : 'Bulanan' }}</p>
                <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Transaksi -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500 flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500 font-medium">Transaksi Selesai</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalTransactions }}</p>
            </div>
        </div>

        <!-- Produk Terjual -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500 flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500 font-medium">Produk Terjual</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalItemsSold }} Pcs</p>
            </div>
        </div>
    </div>

    <!-- TABEL 1: PRODUK TERLARIS -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h4 class="font-bold text-gray-700 flex items-center">
                <svg class="w-5 h-5 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                Produk Terlaris ({{ ($filterType ?? 'monthly') == 'daily' ? 'Harian' : 'Bulanan' }})
            </h4>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-10">Rank</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Terjual</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Pendapatan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($topProducts as $index => $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-center font-bold text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($item->product && $item->product->gambar)
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $item->product->gambar) }}" alt="">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gray-200"></div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $item->product->nama_produk ?? 'Produk Dihapus' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->product->kategori_produk ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ $item->total_qty }} Pcs</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900">Rp {{ number_format($item->total_income, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">Belum ada data penjualan produk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TABEL 2: RINCIAN TRANSAKSI -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h4 class="font-bold text-gray-700">Riwayat Transaksi ({{ ($filterType ?? 'monthly') == 'daily' ? 'Harian' : 'Bulanan' }})</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelanggan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($orders as $order)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">#{{ $order->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->created_at->format('H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $order->user->nama_lengkap ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <ul class="list-disc list-inside text-xs">
                                    @foreach($order->items as $item)
                                        <li>{{ $item->product->nama_produk ?? 'N/A' }} (x{{ $item->quantity }})</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">Tidak ada data penjualan selesai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection