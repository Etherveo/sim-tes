<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi WhatsApp</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
    <link rel="icon" href="{{ asset('Images/fav.png') }}">
</head>
<body class="bg-gray-100 flex min-h-screen items-center justify-center">

    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-md" x-cloak
         x-data="{ 
             timeLeft: {{ \Carbon\Carbon::now()->diffInSeconds($expiresAt, false) > 0 ? \Carbon\Carbon::now()->diffInSeconds($expiresAt, false) : 0 }},
             timerInterval: null,
             init() {
                 if (this.timeLeft > 0) {
                     this.timerInterval = setInterval(() => {
                         if (this.timeLeft > 0) {
                             this.timeLeft--;
                         } else {
                             clearInterval(this.timerInterval);
                         }
                     }, 1000);
                 }
             },
             formatTime(seconds) {
                 const m = Math.floor(seconds / 60);
                 const s = seconds % 60;
                 // Pad minutes and seconds to always show MM:SS
                 const mm = String(m).padStart(2, '0');
                 const ss = String(s).padStart(2, '0');
                 return `${mm}:${ss}`;
             }
         }" x-init="init()"> 

        <div class="text-center mb-6">
            <img class="h-12 w-auto mx-auto mb-4" src="/images/logo-ira.png" alt="Logo">
            
            <h2 class="text-2xl font-bold text-gray-900">Verifikasi Nomor HP</h2>
            <p class="text-sm text-gray-600 mt-2">
                Masukkan kode OTP yang dikirim ke: <br>
                <strong>{{ session('target_phone') ?? (Auth::user()->no_telepon ?? '...') }}</strong>
            </p>
        </div>

        @if (session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-700 text-sm rounded text-center">
                {{ session('error') }}
            </div>
        @endif
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 text-sm rounded text-center">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('otp.verify') }}" method="POST">
            @csrf
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2 text-center">Kode OTP</label>
                <input type="text" name="otp" placeholder="XXXXXX" maxlength="6"
                       class="w-full text-center text-3xl tracking-[0.5em] p-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 font-bold" required>
            </div>

            <div class="text-center mb-4 text-sm" x-show="timeLeft > 0" x-cloak>
                Sisa waktu: <span class="font-bold text-red-600" x-text="formatTime(timeLeft)" x-cloak></span>
            </div>
            
            <div class="text-center mb-4 text-sm text-red-600 font-bold" x-show="timeLeft <= 0" x-cloak>
                Waktu habis! Silakan kirim ulang kode.
            </div>

            <button type="submit" 
                    :disabled="timeLeft <= 0"
                    :class="timeLeft <= 0 ? 'bg-gray-400 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700'"
                    class="w-full text-white py-3 rounded-lg font-bold transition">
                Verifikasi
            </button>
        </form>

        <div class="mt-4 text-center" x-show="timeLeft <= 0" x-cloak>
            <form action="{{ route('otp.resend') }}" method="POST">
                @csrf
                <button type="submit" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">
                    Kirim Ulang Kode OTP
                </button>
            </form>
        </div>

        <div class="mt-6 border-t pt-4 text-center">
            <p class="text-xs text-gray-500 mb-2">Salah nomor telepon?</p>
            
            @auth
                 <a href="{{ route('user.profil') }}" class="text-sm text-gray-500 hover:text-gray-900 underline">Ganti di Profil</a>
            @else
                <a href="{{ route('register') }}" class="text-sm text-gray-500 hover:text-gray-900 underline">
                    Kembali & Ganti Nomor
                </a>
            @endauth
        </div>
    </div>

</body>
</html>