@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-4xl py-12 px-4 sm:px-6 lg:px-8">
    
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Notifikasi Saya</h1>
        
        @if(Auth::user()->unreadNotifications->count() > 0)
            <form action="{{ route('user.notifications.readAll') }}" method="POST">
                @csrf
                <button class="text-sm text-indigo-600 hover:text-indigo-800 hover:underline">
                    Tandai semua sudah dibaca
                </button>
            </form>
        @endif
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <ul class="divide-y divide-gray-200">
            @forelse ($notifications as $notification)
                <li class="p-4 hover:bg-gray-50 transition duration-150 ease-in-out {{ $notification->read_at ? '' : 'bg-blue-50' }}">
                    <a href="{{ route('user.notifications.read', $notification->id) }}" class="flex items-start">
                        <div class="flex-shrink-0 pt-1">
                            <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-900">
                                {{ $notification->data['message'] }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>
                        @if(!$notification->read_at)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Baru
                            </span>
                        @endif
                    </a>
                </li>
            @empty
                <li class="p-12 text-center text-gray-500">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                    <p>Tidak ada notifikasi.</p>
                </li>
            @endforelse
        </ul>
        
        <div class="p-4">
            {{ $notifications->links() }}
        </div>
    </div>
</div>
@endsection
