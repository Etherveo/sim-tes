<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Ira Stationery</title>
        <style>
            [x-cloak] { display: none !important; }
        </style>
        <link rel="icon" href="{{ asset('Images/fav.png') }}">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gray-100 flex flex-col min-h-screen">
        
        <nav class="bg-white shadow-md sticky top-0 z-50 flex-shrink-0" x-data="{ openSearch: false, openMobileMenu: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ url('/') }}">
                            @if(isset($themeSettings['site_logo']) && $themeSettings['site_logo'])
                                <img class="h-10 w-auto" src="{{ asset('storage/' . $themeSettings['site_logo']) }}" alt="Logo Instansi">
                                @else
                                <img class="h-10 md:h-16 w-auto" src="/images/logo-ira.png" alt="Logo Instansi">
                            @endif
                        </a>
                    </div>
                    
                    <div class="hidden md:flex items-center">
                        
                        <div class="flex items-center space-x-2 sm:space-x-4">
                            <a href="{{ route('kategori.index') }}" class="text-gray-600 hover:text-indigo-600 px-3 py-2 rounded-md text-base font-medium">Kategori</a>
                            <button @click="openSearch = !openSearch" class="p-2 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none">
                                <span class="sr-only">Cari</span>
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            </button>
                        </div>
                        <div x-show="openSearch" @click.away="openSearch = false" x-transition:enter="transition-all ease-out duration-300" x-transition:enter-start="opacity-0 w-0" x-transition:enter-end="opacity-100 w-48 sm:w-64" x-transition:leave="transition-all ease-in duration-200" x-transition:leave-start="opacity-100 w-48 sm:w-64" x-transition:leave-end="opacity-0 w-0" class="overflow-hidden ml-2" x-cloak>
                            <form action="{{ route('produk.cari') }}" method="GET" class="relative">
                                <input type="text" name="search" placeholder="Cari produk..." class="w-full p-2 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500" autofocus>
                            </form>
                        </div>

                        <span class="w-px h-6 bg-gray-200 mx-4"></span>

                        @if (Auth::guest() || (Auth::check() && Auth::user()->role == 'pengguna'))
                            <a href="{{ Auth::check() ? route('user.notifications.index') : route('login') }}" class="p-2 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 relative">
                                <span class="sr-only">Notifikasi</span>
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                                @if(Auth::check() && Auth::user()->unreadNotifications->count() > 0)
                                    <span class="absolute top-2 right-2 block h-2.5 w-2.5 rounded-full bg-red-600 ring-2 ring-white"></span>
                                @endif
                            </a>
                            
                            <a href="{{ Auth::check() ? route('keranjang.index') : route('login') }}" class="p-2 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 relative">
                                <span class="sr-only">Keranjang</span>
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                @if(Auth::check())
                                    <span x-show="$store.cart.count > 0" x-cloak
                                          x-text="$store.cart.count"
                                          class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                                    </span>
                                @endif
                            </a>
                        @endif

                        <div class="flex items-center space-x-2 sm:space-x-4 ml-4"> 
                            @auth
                                @if(Auth::user()->role == 'pengguna')
                                    <div x-data="{ openProfile: false }" class="relative">
                                        <button @click="openProfile = !openProfile" class="flex items-center text-gray-600 hover:text-indigo-600 px-3 py-2 rounded-md text-base font-medium focus:outline-none">
                                            <svg class="h-6 w-6 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                            <span>{{ Str::limit(Auth::user()->username, 15) }}</span>
                                            <svg class="h-5 w-5 ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                        </button>
                                        <div x-show="openProfile" @click.away="openProfile = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-20" x-transition x-cloak>
                                            <a href="{{ route('riwayat.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">Riwayat Pesanan</a>
                                            <a href="{{ route('user.profil') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">Profil Saya</a>
                                            <div class="border-t border-gray-100 my-1"></div>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">Keluar</button>
                                            </form>
                                        </div>
                                    </div>
                                @elseif(Auth::user()->role == 'admin')
                                    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-indigo-600 px-3 py-2 rounded-md text-base font-medium">
                                        Admin Dashboard
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center text-gray-600 hover:text-indigo-600 px-3 py-2 rounded-md text-base font-medium">
                                            <span>Keluar</span>
                                        </button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="flex items-center text-gray-600 hover:text-indigo-600 px-3 py-2 rounded-md text-base font-medium">
                                    <svg class="h-5 w-5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    <span>Login</span>
                                </a>
                                <a href="{{ route('register') }}" class="flex items-center text-gray-600 hover:text-indigo-600 px-3 py-2 rounded-md text-base font-medium">
                                    <svg class="h-5 w-5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                                    <span>Register</span>
                                </a>
                            @endauth
                        </div>
                    </div>
                    
                    <div class="flex items-center md:hidden">
                        @if (Auth::guest() || (Auth::check() && Auth::user()->role == 'pengguna'))
                            <a href="{{ Auth::check() ? route('user.notifications.index') : route('login') }}" class="p-2 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 relative">
                                <span class="sr-only">Notifikasi</span>
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                                @if(Auth::check() && Auth::user()->unreadNotifications->count() > 0)
                                    <span class="absolute top-2 right-2 block h-2.5 w-2.5 rounded-full bg-red-600 ring-2 ring-white"></span>
                                @endif
                            </a>
                            <a href="{{ Auth::check() ? route('keranjang.index') : route('login') }}" class="p-2 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 relative">
                                <span class="sr-only">Keranjang</span>
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                @if(Auth::check())
                                    <span x-show="$store.cart.count > 0" x-cloak
                                          x-text="$store.cart.count"
                                          class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                                    </span>
                                @endif
                            </a>
                        @endif
                        <button @click="openMobileMenu = !openMobileMenu" class="p-2 ml-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none">
                            <span class="sr-only">Buka menu</span><svg x-show="!openMobileMenu" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                            <svg x-show="openMobileMenu" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div x-show="openMobileMenu" @click.away="openMobileMenu = false" class="md:hidden border-t border-gray-200" x-cloak>
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                    <form action="{{ route('produk.cari') }}" method="GET" class="px-2 pb-2">
                        <input type="text" name="search" placeholder="Cari produk..." class="w-full p-2 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </form>
                    <a href="{{ route('kategori.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">Kategori</a>
                    @auth
                        @if(Auth::user()->role == 'pengguna')
                            <a href="{{ route('riwayat.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">Riwayat Pesanan</a>
                            <a href="{{ route('user.profil') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">Profil Saya</a>
                        @elseif(Auth::user()->role == 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">Admin Dashboard</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">Keluar</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">Login</a>
                        <a href="{{ route('register') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">Register</a>
                    @endauth
                </div>
            </div>
        </nav>

        <main class="flex-grow">
            @yield('content')
        </main>

        <x-footer />

    </body>
</html>