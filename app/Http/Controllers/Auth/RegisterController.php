<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        // 1. Validasi Input
        $validatedData = $request->validate([
            'nama_lengkap'  => ['required', 'string', 'max:255'],
            'username'      => ['required', 'string', 'max:255', 'unique:users'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'no_telepon'    => ['required', 'string', 'min:10', 'max:15', 'unique:users'],
            'alamat'        => ['required', 'string'],
            'jenis_kelamin' => ['required', 'string', Rule::in(['Laki-laki', 'Perempuan'])],
            'password'      => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        
        $validatedData['password'] = Hash::make($validatedData['password']);
        
        Session::put('temp_registration_data', $validatedData);

        // 3. Generate OTP & Waktu Kadaluarsa (2 Menit)
        $otp = rand(100000, 999999);
        $expiresAt = now()->addMinutes(2); // Kadaluarsa dalam 2 menit

        // 4. Kirim WA
        $waService = new WhatsAppService();
        $waService->sendOTP($validatedData['no_telepon'], $otp);

        // 5. Simpan OTP & Info Waktu ke Session
        Session::put('otp_code', $otp);
        Session::put('otp_expires_at', $expiresAt);
        Session::put('target_phone', $validatedData['no_telepon']);

        // 6. Redirect ke Halaman Verifikasi
        return redirect()->route('otp.show');
    }
}