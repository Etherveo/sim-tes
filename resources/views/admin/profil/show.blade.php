@extends('layouts.admin')

@section('content')
    
    <h3 class="text-3xl font-medium text-gray-700">Profil Saya</h3>

    <div class="mt-8">
        <div class="p-6 bg-white rounded-lg shadow-md">
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="nama_lengkap">
                    Nama Lengkap
                </label>
                <input type="text" id="nama_lengkap" 
                       value="{{ $user->nama_lengkap }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-gray-700 bg-gray-100 focus:outline-none" 
                       disabled> </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                    Username
                </label>
                <input type="text" id="username" 
                       value="{{ $user->username }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-gray-700 bg-gray-100 focus:outline-none" 
                       disabled>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                    Email
                </label>
                <input type="email" id="email" 
                       value="{{ $user->email }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-gray-700 bg-gray-100 focus:outline-none" 
                       disabled>
            </div>
            
            <div class="flex justify-end space-x-4">
                </div>

        </div>
    </div>

@endsection