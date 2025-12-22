@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-3xl font-medium text-gray-700">Detail Pesanan #{{ $order->id }}</h3>
        <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white shadow rounded-lg p-6">
                <h4 class="text-lg font-semibold mb-4 border-b border-gray-300 pb-2">Informasi Pelanggan</h4>
                <p class="text-sm text-gray-500 mb-1">Nama Lengkap</p>
                <p class="font-medium text-gray-900 mb-3">{{ $order->user->nama_lengkap ?? 'User Terhapus' }}</p>
                
                <p class="text-sm text-gray-500 mb-1">Email</p>
                <p class="font-medium text-gray-900 mb-3">{{ $order->user->email ?? '-' }}</p>

                <p class="text-sm text-gray-500 mb-1">Nomor Telepon</p>
                <p class="font-medium text-gray-900 mb-3">{{ $order->user->no_telepon ?? '-' }}</p>

                <p class="text-sm text-gray-500 mb-1">Alamat</p>
                <p class="font-medium text-gray-900 mb-3">{{ $order->user->alamat ?? '-' }}</p>
                
                <p class="text-sm text-gray-500 mb-1">Tanggal Pesanan</p>
                <p class="font-medium text-gray-900">{{ $order->created_at->format('d F Y, H:i') }}</p>
            </div>
            
            <div class="bg-white shadow rounded-lg p-6">
                <h4 class="text-lg font-semibold mb-4 border-b border-gray-300 pb-2">Jadwal Pengambilan</h4>
                @if($order->pickup_date)
                    <p class="text-sm text-gray-500 mb-1">Tanggal</p>
                    <p class="font-medium text-indigo-700 mb-3">{{ \Carbon\Carbon::parse($order->pickup_date)->format('d F Y') }}</p>
                    
                    <p class="text-sm text-gray-500 mb-1">Jam</p>
                    <p class="font-medium text-indigo-700">{{ \Carbon\Carbon::parse($order->pickup_time)->format('H:i') }}</p>
                @else
                    <p class="text-gray-500 italic">Tidak ada data jadwal.</p>
                @endif
            </div>
            
            <!-- Card Status -->
            <div class="bg-white shadow rounded-lg p-6">
                <h4 class="text-lg font-semibold mb-4 border-b border-gray-300 pb-2">Status Pesanan</h4>
                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                    {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                      ($order->status === 'ready' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                    @if($order->status == 'pending') Sedang Disiapkan
                    @elseif($order->status == 'ready') Siap Diambil
                    @elseif($order->status == 'completed') Selesai
                    @else {{ ucfirst($order->status) }}
                    @endif
                </span>
            </div>
        </div>

        <!-- Daftar Produk -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="p-6 border-b border-gray-300">
                    <h4 class="text-lg font-semibold">Item yang Dibeli</h4>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Harga</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qty</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 border rounded overflow-hidden">
                                            @if($item->product && $item->product->gambar)
                                                <img class="h-10 w-10 object-cover" src="{{ asset('storage/' . $item->product->gambar) }}" alt="">
                                            @else
                                                <div class="h-10 w-10 bg-gray-200 flex items-center justify-center text-xs">N/A</div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $item->product->nama_produk ?? 'Produk Dihapus' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $item->product->kategori_produk ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                    Rp {{ number_format($item->price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900">
                                    Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                        <!-- Baris Total -->
                        <tr class="bg-gray-50">
                            <td colspan="3" class="px-6 py-4 text-right font-bold text-gray-900">TOTAL PEMBAYARAN</td>
                            <td class="px-6 py-4 text-right font-bold text-indigo-700 text-lg">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection