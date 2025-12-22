@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-3xl font-medium text-gray-700">Pemberitahuan</h3>
        
        @if($unreadNotifications->count() > 0)
            <form action="{{ route('admin.notifications.readAll') }}" method="POST">
                @csrf
                <button class="text-sm text-indigo-600 hover:text-indigo-800 hover:underline bg-white px-4 py-2 rounded-md shadow-sm">
                    Tandai semua sudah dibaca
                </button>
            </form>
        @endif
    </div>

    @if($unreadNotifications->count() > 0)
        <div class="mb-8">
            <h4 class="text-lg font-semibold text-gray-700 mb-3 flex items-center">
                <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                Belum Dibaca ({{ $unreadNotifications->count() }})
            </h4>
            <div class="bg-white shadow-md rounded-lg overflow-hidden border border-blue-100">
                <ul class="divide-y divide-blue-100">
                    @foreach ($unreadNotifications as $notification)
                        <li class="p-4 hover:bg-blue-50 transition duration-150 ease-in-out bg-blue-50">
                            <a href="{{ route('admin.notifications.read', $notification->id) }}" class="flex justify-between items-start">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 mt-1">
                                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-bold text-gray-900">
                                            {{ $notification->data['message'] }}
                                        </p>
                                        <p class="text-sm text-gray-600 mt-1">
                                            Total Pesanan: <span class="font-semibold">Rp {{ number_format($notification->data['total_price'], 0, ',', '.') }}</span>
                                        </p>
                                        <p class="text-xs text-indigo-600 mt-2 font-medium">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Baru
                                    </span>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div>
        <h4 class="text-lg font-semibold text-gray-600 mb-3 flex items-center">
            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Riwayat Notifikasi
        </h4>
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <ul class="divide-y divide-gray-200">
                @forelse ($readNotifications as $notification)
                    <li class="p-4 hover:bg-gray-50 transition duration-150 ease-in-out opacity-75">
                        <div class="flex justify-between items-start">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mt-1">
                                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm text-gray-600">
                                        {{ $notification->data['message'] }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $notification->created_at->format('d M Y, H:i') }} 
                                        ({{ $notification->created_at->diffForHumans() }})
                                    </p>
                                </div>
                            </div>
                        </div>
                    </li>
                @empty
                    @if($unreadNotifications->count() == 0)
                        <li class="p-10 text-center text-gray-500 flex flex-col items-center">
                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            <p>Belum ada riwayat pemberitahuan.</p>
                        </li>
                    @endif
                @endforelse
            </ul>
        </div>
    </div>
@endsection