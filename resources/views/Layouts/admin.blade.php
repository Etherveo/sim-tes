<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - IRA STATIONERY</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <link rel="icon" href="{{ asset('Images/fav.png') }}">
</head>
<body class="bg-gray-100">
    
    <div x-data="{ isSidebarOpen: false }" class="flex h-screen bg-gray-200">
        
        <div x-show="isSidebarOpen" @click="isSidebarOpen = false" class="fixed inset-0 z-20 bg-black opacity-50 transition-opacity lg:hidden" x-cloak></div>
        
        <aside 
            :class="isSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-30 w-64 px-4 py-7 overflow-y-auto bg-gray-800 text-gray-300 transform transition duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0"
            x-cloak>
            
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-2">
                @if(isset($themeSettings['site_logo']) && $themeSettings['site_logo'])
                    <img class="h-10 w-auto" src="{{ asset('storage/' . $themeSettings['site_logo']) }}" alt="Logo">
                @else
                    <img class="h-10 w-auto" src="/images/logo-ira.png" alt="Logo">
                @endif
            </a>

            <nav class="mt-10">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center mt-4 py-2 px-2 rounded transition-colors duration-200
                          {{ request()->routeIs('admin.dashboard*') || request()->routeIs('admin.products*') ? 'bg-gray-900 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    <span class="mx-4 font-medium">Daftar Produk</span>
                </a>

                <a href="{{ route('admin.categories.index') }}" 
                   class="flex items-center mt-4 py-2 px-2 rounded transition-colors duration-200
                          {{ request()->routeIs('admin.categories*') ? 'bg-gray-900 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <span class="mx-4 font-medium">Kategori</span>
                </a>
                
                <a href="{{ route('admin.orders.index') }}" 
                   class="flex items-center mt-4 py-2 px-2 rounded transition-colors duration-200
                          {{ request()->routeIs('admin.orders*') ? 'bg-gray-900 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                    <span class="mx-4 font-medium">Daftar Pesanan</span>
                </a>

                <a href="{{ route('admin.notifications.index') }}" 
                   class="flex items-center mt-4 py-2 px-2 rounded transition-colors duration-200 relative
                          {{ request()->routeIs('admin.notifications*') ? 'bg-gray-900 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                    <span class="mx-4 font-medium">Pemberitahuan</span>
                    @php $unreadCount = Auth::user()->unreadNotifications->count(); @endphp
                    @if($unreadCount > 0)
                        <span class="absolute right-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded-full">{{ $unreadCount }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.reports.index') }}" 
                   class="flex items-center mt-4 py-2 px-2 rounded transition-colors duration-200
                          {{ request()->routeIs('admin.reports*') ? 'bg-gray-900 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    <span class="mx-4 font-medium">Laporan</span>
                </a>

                <a href="{{ route('admin.themes.index') }}" 
                   class="flex items-center mt-4 py-2 px-2 rounded transition-colors duration-200
                          {{ request()->routeIs('admin.themes*') ? 'bg-gray-900 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="mx-4 font-medium">Tema Aplikasi</span>
                </a>
                
                <a href="{{ route('admin.profil') }}" 
                   class="flex items-center mt-4 py-2 px-2 rounded transition-colors duration-200
                          {{ request()->routeIs('admin.profil') ? 'bg-gray-900 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    <span class="mx-4 font-medium">Profil</span>
                </a>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                       class="w-full flex items-center mt-4 py-2 px-2 rounded transition-colors duration-200 text-gray-400 hover:bg-gray-700 hover:text-white">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H3" /></svg>
                        <span class="mx-4 font-medium">Keluar Akun</span>
                    </button>
                </form>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="flex items-center justify-between p-4 bg-white border-b lg:hidden">
                <button @click="isSidebarOpen = true" class="text-gray-500 focus:outline-none">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <span class="text-xl font-bold">Admin Dashboard</span>
                <div></div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
                <div class="container mx-auto px-6 py-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

</body>
</html>