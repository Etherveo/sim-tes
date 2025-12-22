@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-7xl py-12 px-4 sm:px-6 lg:px-8">
    
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Riwayat Pesanan Saya</h1>

    @if($orders->isEmpty())
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            <h2 class="text-xl font-medium text-gray-900">Belum Ada Pesanan</h2>
            <p class="text-gray-500 mt-2">Anda belum pernah melakukan pemesanan.</p>
            <a href="{{ url('/') }}" class="mt-6 inline-block bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700">
                Mulai Belanja
            </a>
        </div>
    @else
        <div class="space-y-6">
            @foreach($orders as $order)
                <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200" x-data="{ expanded: false }">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 grid grid-cols-2 gap-y-3 gap-x-4 sm:flex sm:justify-between sm:items-center">
                        <div>
                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider block">ID Pesanan</span>
                            <p class="text-sm font-bold text-gray-900">#{{ $order->id }}</p>
                        </div>
                        <div class="sm:text-left">
                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider block">Tanggal</span>
                            <p class="text-sm text-gray-900">{{ $order->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider block">Total</span>
                            <p class="text-sm font-bold text-indigo-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                        </div>
    
                        <div class="sm:text-right">
                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider block">Status</span>
                            <div class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                      ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                                      ($order->status === 'ready' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                    @if($order->status == 'pending') Sedang Disiapkan
                                    @elseif($order->status == 'ready') Siap Diambil
                                    @elseif($order->status == 'completed') Selesai
                                    @elseif($order->status == 'cancelled') Dibatalkan
                                    @else {{ ucfirst($order->status) }}
                                    @endif
                                </span>
                            </div>
                        </div>

                    </div>
                    @if($order->pickup_date)
                        <div class="px-4 py-2 bg-blue-50 border-b border-blue-100 text-xs sm:text-sm text-blue-800 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Ambil: <strong>{{ \Carbon\Carbon::parse($order->pickup_date)->format('d M Y') }}</strong>, Pukul <strong>{{ \Carbon\Carbon::parse($order->pickup_time)->format('H:i') }}</strong></span>
                        </div>
                    @endif
                    <div class="px-4 py-4 sm:px-6">
                        <ul class="divide-y divide-gray-200">
                            @if($order->items->count() > 0)
                                @php $firstItem = $order->items->first(); @endphp
                                <li class="py-3 sm:py-4 flex items-center">
                                    <div class="flex-shrink-0 w-14 h-14 sm:w-16 sm:h-16 border border-gray-200 rounded-md overflow-hidden">
                                        @if($firstItem->product && $firstItem->product->gambar)
                                            <img src="{{ asset('storage/' . $firstItem->product->gambar) }}" alt="{{ $firstItem->product->nama_produk }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-gray-100 flex items-center justify-center text-xs text-gray-400">N/A</div>
                                        @endif
                                    </div>
                                    <div class="ml-3 sm:ml-4 flex-1">
                                        <h4 class="text-sm font-medium text-gray-900 line-clamp-1">{{ $firstItem->product->nama_produk ?? 'Produk tidak tersedia' }}</h4>
                                        <p class="text-xs text-gray-500">{{ $firstItem->product->kategori_produk ?? '-' }}</p>
                                        <div class="flex justify-between items-center mt-1">
                                            <p class="text-xs sm:text-sm text-gray-600">{{ $firstItem->quantity }} x Rp {{ number_format($firstItem->price, 0, ',', '.') }}</p>
                                            <p class="text-xs sm:text-sm font-medium text-gray-900">Rp {{ number_format($firstItem->quantity * $firstItem->price, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                </li>
                            @endif
                            @if($order->items->count() > 1)
                                <div x-show="expanded" x-collapse x-cloak class="border-t border-gray-200 mt-2">
                                    @foreach($order->items->skip(1) as $item)
                                        <li class="py-3 sm:py-4 flex items-center border-b border-gray-200 last:border-0">
                                            <div class="flex-shrink-0 w-14 h-14 sm:w-16 sm:h-16 border border-gray-200 rounded-md overflow-hidden">
                                                @if($item->product && $item->product->gambar)
                                                    <img src="{{ asset('storage/' . $item->product->gambar) }}" alt="{{ $item->product->nama_produk }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full bg-gray-100 flex items-center justify-center text-xs text-gray-400">N/A</div>
                                                @endif
                                            </div>
                                            <div class="ml-3 sm:ml-4 flex-1">
                                                <h4 class="text-sm font-medium text-gray-900 line-clamp-1">{{ $item->product->nama_produk ?? 'Produk tidak tersedia' }}</h4>
                                                <p class="text-xs text-gray-500">{{ $item->product->kategori_produk ?? '-' }}</p>
                                                <div class="flex justify-between items-center mt-1">
                                                    <p class="text-xs sm:text-sm text-gray-600">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                                    <p class="text-xs sm:text-sm font-medium text-gray-900">Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</p>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </div>
                                <div class="mt-3 text-center">
                                    <button @click="expanded = !expanded" 
                                            class="text-xs sm:text-sm font-medium text-indigo-600 hover:text-indigo-800 focus:outline-none flex items-center justify-center w-full py-2">
                                        <span x-show="!expanded">Lihat {{ $order->items->count() - 1 }} produk lainnya</span>
                                        <span x-show="expanded">Tutup</span>
                                        <svg x-show="!expanded" class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        <svg x-show="expanded" class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                    </button>
                                </div>
                            @endif

                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection