@extends('layouts.admin')

@section('content')
    <h3 class="text-3xl font-medium text-gray-700 mb-6">Buat Tema Baru</h3>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded relative">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white p-6 rounded-lg shadow-md max-w-2xl">
        <!-- Form mengarah ke route 'admin.themes.store' -->
        <form action="{{ route('admin.themes.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Nama Tema -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Tema</label>
                <input type="text" name="name" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-indigo-500" placeholder="Contoh: Tema Lebaran" required>
            </div>

            <div class="grid grid-cols-1 gap-6 mb-6">
                <!-- Logo -->
                <div class="border p-4 rounded-lg bg-gray-50">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Logo Instansi (Navbar)</label>
                    <input type="file" name="site_logo" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                    <p class="text-xs text-gray-400 mt-1">Format: PNG (Transparan), JPG. Max 2MB.</p>
                </div>

                <!-- Login Image -->
                <div class="border p-4 rounded-lg bg-gray-50">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Gambar Halaman Login</label>
                    <input type="file" name="login_image" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                </div>

                <!-- Register Image -->
                <div class="border p-4 rounded-lg bg-gray-50">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Gambar Halaman Register</label>
                    <input type="file" name="register_image" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('admin.themes.index') }}" class="px-4 py-2 bg-gray-200 rounded text-gray-700 hover:bg-gray-300">Batal</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded font-bold hover:bg-indigo-700 shadow-md transition duration-200">
                    Simpan Tema
                </button>
            </div>
        </form>
    </div>
@endsection