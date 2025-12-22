@extends('layouts.admin')

@section('content')
    <h3 class="text-3xl font-medium text-gray-700 mb-6">Edit Kategori</h3>

    <div class="bg-white rounded-lg shadow-md p-6 max-w-lg">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Kategori</label>
                <input type="text" name="nama_kategori" value="{{ $category->nama_kategori }}" class="w-full px-3 py-2 border rounded-md" required>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Gambar Kategori (Opsional)</label>
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $category->gambar) }}" class="h-20 w-20 rounded object-cover">
                </div>
                <input type="file" name="gambar" class="w-full px-3 py-2 border rounded-md">
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">Batal</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Update</button>
            </div>
        </form>
    </div>
@endsection