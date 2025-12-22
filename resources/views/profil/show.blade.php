@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-4xl py-12 px-4 sm:px-6 lg:px-8">
    
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Profil Saya</h1>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded relative">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded relative">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow rounded-lg overflow-hidden" x-data="{ isEditing: {{ $errors->any() ? 'true' : 'false' }} }">
        
        <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center bg-gray-50">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Informasi Akun</h3>
            
            <button @click="isEditing = true" 
                    x-show="!isEditing" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
                Ubah Profil
            </button>

            <button @click="isEditing = false" 
                    x-show="isEditing" 
                    x-cloak
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Batal
            </button>
        </div>

        <div class="p-6" x-show="!isEditing">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Nama Lengkap</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $user->nama_lengkap }}</dd>
                </div>

                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Username</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono bg-gray-100 px-2 py-1 rounded inline-block">
                        {{ $user->username }}
                    </dd>
                </div>

                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Alamat Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                </div>

                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Nomor WhatsApp</dt>
                    <dd class="mt-1 flex items-center">
                        <span class="text-sm text-gray-900 mr-2">{{ $user->no_telepon }}</span>
                        
                        @if($user->no_telepon_verified_at)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                <svg class="mr-1.5 h-3 w-3 text-green-600" fill="currentColor" viewBox="0 0 8 8"><path fill-rule="evenodd" d="M1.99 7.02c-.31 0-.61-.13-.82-.37L.17 5.48c-.28-.33-.23-.83.1-1.11.33-.28.83-.23 1.11.1l.66.79 3.03-4.24c.24-.34.72-.41 1.06-.17.34.24.41.72.17 1.06l-3.5 4.9c-.2.28-.53.44-.88.44z0 0 1 0 2zm3-2a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" clip-rule="evenodd" /></svg>
                                Terverifikasi
                            </span>
                        @else
                            <a href="{{ route('otp.show') }}" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 hover:bg-yellow-200 cursor-pointer">
                                <svg class="mr-1.5 h-3 w-3 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                Belum Verifikasi
                            </a>
                        @endif
                    </dd>
                </div>

                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Jenis Kelamin</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user->jenis_kelamin }}</dd>
                </div>

                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Alamat Lengkap</dt>
                    <dd class="mt-1 text-sm text-gray-900 border p-3 rounded bg-gray-50">
                        {{ $user->alamat ?? '-' }}
                    </dd>
                </div>

            </dl>
        </div>

        <div x-show="isEditing" x-cloak>
            <form action="{{ route('user.profil.update') }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    
                    <div>
                        <label for="nama_lengkap" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border p-2">
                    </div>

                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-500">Username</label>
                        <input type="text" value="{{ $user->username }}" disabled class="mt-1 block w-full border-gray-200 bg-gray-100 text-gray-500 rounded-md shadow-sm sm:text-sm border p-2 cursor-not-allowed">
                        <input type="hidden" name="username" value="{{ $user->username }}">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-500">Email</label>
                        <input type="email" value="{{ $user->email }}" disabled class="mt-1 block w-full border-gray-200 bg-gray-100 text-gray-500 rounded-md shadow-sm sm:text-sm border p-2 cursor-not-allowed">
                        <input type="hidden" name="email" value="{{ $user->email }}">
                    </div>

                    <div>
                        <label for="no_telepon" class="block text-sm font-medium text-gray-500">Nomor WhatsApp</label>
                        <input type="text" value="{{ $user->no_telepon }}" disabled class="mt-1 block w-full border-gray-200 bg-gray-100 text-gray-500 rounded-md shadow-sm sm:text-sm border p-2 cursor-not-allowed">
                        <p class="mt-1 text-xs text-gray-400">Nomor telepon tidak dapat diubah.</p>
                        <input type="hidden" name="no_telepon" value="{{ $user->no_telepon }}">
                    </div>

                    <div>
                        <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                        <select id="jenis_kelamin" name="jenis_kelamin" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md border">
                            <option value="Laki-laki" {{ old('jenis_kelamin', $user->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('jenis_kelamin', $user->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                    <textarea name="alamat" id="alamat" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border p-2">{{ old('alamat', $user->alamat) }}</textarea>
                </div>

                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Ubah Password</h3>
                    <p class="mt-1 text-sm text-gray-500">Biarkan kosong jika tidak ingin mengubah password.</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700">Password Saat Ini</label>
                        <input type="password" name="current_password" id="current_password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border p-2">
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                            <input type="password" name="new_password" id="new_password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border p-2">
                        </div>

                        <div>
                            <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border p-2">
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex justify-end space-x-3">
                    <button type="button" @click="isEditing = false" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Batal
                    </button>
                    <button type="submit" class="bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection