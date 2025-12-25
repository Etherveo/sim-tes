@extends('layouts.admin')

@section('content')
    
    <div x-data="{ showDeleteModal: false, deleteActionUrl: '' }">

        <div class="flex justify-between items-center mb-6">
            <h3 class="text-3xl font-medium text-gray-700">Daftar Tema</h3>
            <a href="{{ route('admin.themes.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                + Tambah Tema Baru
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded relative">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded relative">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-gray-100 rounded-lg shadow-inner border-2 border-dashed border-gray-300 p-4 flex flex-col justify-center items-center text-center opacity-75">
                <h4 class="font-bold text-gray-600">Tema Bawaan (Default)</h4>
                <p class="text-xs text-gray-500 mt-2 mb-4">Tampilan asli kodingan jika tidak ada tema aktif.</p>
                @if($themes->where('is_active', true)->isEmpty())
                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-bold border border-green-200">SEDANG AKTIF</span>
                @else
                    <span class="text-xs text-gray-400">Nonaktif</span>
                @endif
            </div>

            @forelse($themes as $theme)
                <div class="bg-white rounded-lg shadow-md overflow-hidden border-2 {{ $theme->is_active ? 'border-green-500 ring-2 ring-green-100' : 'border-transparent' }}">
                    
                    <div class="h-40 bg-gray-200 relative group">
                        <img src="{{ asset('storage/' . $theme->login_image) }}" class="w-full h-full object-cover opacity-90 transition group-hover:opacity-100">
                        
                        <div class="absolute bottom-2 left-2 bg-white p-1 rounded shadow">
                            <img src="{{ asset('storage/' . $theme->site_logo) }}" class="h-8 w-auto">
                        </div>

                        @if($theme->is_active)
                            <div class="absolute top-2 right-2 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded shadow">
                                AKTIF
                            </div>
                        @endif
                    </div>

                    <div class="p-4">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h4 class="font-bold text-lg text-gray-800">{{ $theme->name }}</h4>
                                <p class="text-xs text-gray-500">Dibuat: {{ $theme->created_at->format('d M Y') }}</p>
                            </div>
                        </div>

                        <div class="flex justify-between items-center gap-2">
                            
                            <!-- 1. Tombol Aktivasi / Deaktivasi -->
                            @if(!$theme->is_active)
                                <form action="{{ route('admin.themes.activate', $theme) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full bg-indigo-50 hover:bg-indigo-100 text-indigo-700 py-2 px-2 rounded text-sm font-medium border border-indigo-200 transition">
                                        Pakai Tema
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.themes.deactivate', $theme) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full bg-orange-50 hover:bg-orange-100 text-orange-700 py-2 px-2 rounded text-sm font-medium border border-orange-200 transition flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Matikan
                                    </button>
                                </form>
                            @endif

                            <!-- 2. Tombol Edit -->
                            <a href="{{ route('admin.themes.edit', $theme) }}" class="bg-gray-100 text-gray-600 p-2 rounded hover:bg-yellow-100 hover:text-yellow-700 border border-gray-200 transition" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </a>

                            <!-- 3. Tombol Hapus -->
                            @if(!$theme->is_active)
                                <button type="button" 
                                        @click="deleteActionUrl = '{{ route('admin.themes.destroy', $theme) }}'; showDeleteModal = true"
                                        class="bg-gray-100 text-gray-600 p-2 rounded hover:bg-red-100 hover:text-red-700 border border-gray-200 transition" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            @else
                                <button disabled class="bg-gray-100 text-gray-300 p-2 rounded cursor-not-allowed border border-gray-100" title="Matikan tema dulu sebelum menghapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            @endif

                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-10 flex flex-col items-center justify-center text-gray-400">
                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <p>Belum ada tema kustom yang dibuat.</p>
                </div>
            @endforelse
        </div>

        <div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto" x-cloak>

            <div x-show="showDeleteModal" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-black/50" @click="showDeleteModal = false"></div>

            <div x-show="showDeleteModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 class="relative w-full max-w-md p-6 bg-white rounded-lg shadow-xl m-4 transform transition-all">
                
                <div class="text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 mb-4">
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>

                    <h3 class="text-lg font-medium text-gray-900 mb-2">Hapus Tema?</h3>
                    <p class="text-sm text-gray-500">
                        Apakah Anda yakin ingin menghapus tema ini secara permanen? Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>

                <div class="mt-6 flex justify-center space-x-3">
                    <button @click="showDeleteModal = false" 
                            class="px-4 py-2 bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Batal
                    </button>

                    <form :action="deleteActionUrl" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-sm">
                            Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection