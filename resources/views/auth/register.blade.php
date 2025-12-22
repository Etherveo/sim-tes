<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Baru</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="{{ asset('Images/fav.png') }}">
</head>
<body class="bg-gray-100">

    <a href="{{ url('/') }}" 
       class="absolute top-5 left-5 z-10 p-2 bg-white rounded-full shadow-md text-gray-700 hover:bg-gray-100 transition-colors"
       aria-label="Kembali ke halaman utama">
        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
    </a>

    <div class="flex min-h-screen">
        <div class="hidden md:flex md:w-1/2 bg-gray-200 items-center justify-center">
            @if(isset($themeSettings['register_image']) && $themeSettings['register_image'])
                <img src="{{ asset('storage/' . $themeSettings['register_image']) }}" alt="Register Image" class="h-auto w-3/4 object-contain">
            @else
                <img src="images\logo-ira.png" alt="Register Image" class="h-auto w-3/4 object-contain">
            @endif
        </div>

        <div class="w-full md:w-1/2 flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                
                <div class="text-center mb-8">
                    @if(isset($themeSettings['site_logo']) && $themeSettings['site_logo'])
                        <img class="h-16 w-auto mx-auto" src="{{ asset('storage/' . $themeSettings['site_logo']) }}" alt="Logo Instansi">
                    @else
                        <img class="h-16 w-auto mx-auto" src="/images/logo-ira.png" alt="Logo Instansi">
                    @endif

                    <h2 class="mt-6 text-3xl font-bold text-gray-900">
                        Buat Akun Pengguna Baru
                    </h2>
                </div>
                
                <!-- Alert Error Validasi Register -->
                @if ($errors->any())
                    <div class="mt-4 p-4 bg-red-50 border-l-4 border-red-500 rounded">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-bold text-red-700 mb-2">
                                    Pendaftaran Gagal
                                </p>
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li class="text-sm text-red-600">{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('register') }}" method="POST" class="mt-8">
                    @csrf
                    
                    <div>
                        <label for="nama_lengkap" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <div class="mt-1">
                            <input id="nama_lengkap" name="nama_lengkap" type="text" required value="{{ old('nama_lengkap') }}"
                                   class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                        <div class="mt-1">
                            <input id="username" name="username" type="text" required value="{{ old('username') }}"
                                   class="w-full p-3 border @error('username') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        @error('username')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mt-6">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <div class="mt-1">
                            <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                                   class="w-full p-3 border @error('email') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        @error('email')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-6">
                        <label for="no_telepon" class="block text-sm font-medium text-gray-700">No. WhatsApp (Aktif)</label>
                        <div class="mt-1">
                            <input id="no_telepon" name="no_telepon" type="number" placeholder="08..." required value="{{ old('no_telepon') }}"
                                   class="w-full p-3 border @error('no_telepon') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Kami akan mengirimkan kode OTP ke nomor ini untuk verifikasi.</p>
                        @error('no_telepon')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Alamat -->
                    <div class="mt-6">
                        <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                        <div class="mt-1">
                            <textarea id="alamat" name="alamat" rows="3" required
                                      class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('alamat') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <div class="mt-1">
                            <input id="password" name="password" type="password" autocomplete="new-password" required
                                   class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                        <div class="mt-1">
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                                   class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                        <div class="mt-1">
                            <select id="jenis_kelamin" name="jenis_kelamin" required
                                    class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="" disabled {{ old('jenis_kelamin') ? '' : 'selected' }}>Pilih Jenis Kelamin</option>
                                <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-8">
                        <button type="submit"
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Daftar
                        </button>
                    </div>
                </form>
                <p class="mt-8 text-center text-sm text-gray-600">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Login di sini
                    </a>
                </p>
            </div>
        </div>
    </div>

</body>
</html>