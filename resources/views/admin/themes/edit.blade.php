@extends('layouts.admin')

@section('content')
    <h3 class="text-3xl font-medium text-gray-700 mb-6">Edit Tema: {{ $theme->name }}</h3>

    <div class="bg-white p-6 rounded-lg shadow-md max-w-2xl">
        <form action="{{ route('admin.themes.update', $theme) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Tema</label>
                <input type="text" name="name" value="{{ $theme->name }}" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="grid grid-cols-1 gap-6 mb-6">
                <div class="border p-3 rounded">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Logo Instansi</label>
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $theme->site_logo) }}" class="h-10 border bg-gray-100 p-1">
                    </div>
                    <input type="file" name="site_logo" class="w-full text-sm text-gray-500">
                </div>

                <div class="border p-3 rounded">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Gambar Login</label>
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $theme->login_image) }}" class="h-20 object-cover border">
                    </div>
                    <input type="file" name="login_image" class="w-full text-sm text-gray-500">
                </div>

                <div class="border p-3 rounded">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Gambar Register</label>
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $theme->register_image) }}" class="h-20 object-cover border">
                    </div>
                    <input type="file" name="register_image" class="w-full text-sm text-gray-500">
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.themes.index') }}" class="px-4 py-2 bg-gray-200 rounded text-gray-700">Batal</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Update Tema</button>
            </div>
        </form>
    </div>
@endsection