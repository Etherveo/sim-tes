<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class OtpController extends Controller
{
    public function show()
    {
        // Cek apakah ada data registrasi temporary di session (flow baru)
        if (Session::has('temp_registration_data')) {
            // FLOW REGISTRASI BARU: Data belum disimpan ke database
            // OTP sudah dikirim dari RegisterController::store()
            $expiresAt = Session::get('otp_expires_at');
            return view('auth.verify-otp', compact('expiresAt'));
        }
        
        // FLOW VERIFIKASI EXISTING USER: User sudah login
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('register')->with('error', 'Silakan daftar terlebih dahulu.');
        }

        // Jika sudah verifikasi, tendang ke beranda
        if ($user->no_telepon_verified_at) {
            return redirect('/')->with('success', 'Nomor Anda sudah terverifikasi.');
        }

        // Jika belum ada OTP di sesi, KIRIM OTP BARU untuk user yang sudah login
        if (!Session::has('otp_code')) {
            $otp = rand(100000, 999999);
            $expiresAt = now()->addMinutes(2);
            
            $waService = new WhatsAppService();
            $waService->sendOTP($user->no_telepon, $otp);

            Session::put('otp_code', $otp);
            Session::put('otp_expires_at', $expiresAt);
            Session::put('target_phone', $user->no_telepon);
        }

        $expiresAt = Session::get('otp_expires_at');
        return view('auth.verify-otp', compact('expiresAt'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
        ]);

        // FLOW 1: Registrasi Baru - Data di Session, User belum ada di database
        if (Session::has('temp_registration_data')) {
            $sessionOtp = session('otp_code');
            $expiresAt = session('otp_expires_at');

            // Validasi OTP
            if (!$sessionOtp || !$expiresAt) {
                return back()->with('error', 'Sesi OTP tidak valid. Silakan daftar ulang.');
            }

            if (now()->greaterThan($expiresAt)) {
                return back()->with('error', 'Kode OTP sudah kadaluarsa. Silakan minta kode baru.');
            }

            if ($request->otp != $sessionOtp) {
                return back()->with('error', 'Kode OTP salah. Silakan periksa WhatsApp Anda.');
            }

            // OTP BENAR: Buat user dari data session
            $data = Session::get('temp_registration_data');
            $user = \App\Models\User::create([
                'nama_lengkap'  => $data['nama_lengkap'],
                'username'      => $data['username'],
                'email'         => $data['email'],
                'no_telepon'    => $data['no_telepon'],
                'alamat'        => $data['alamat'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'password'      => $data['password'], // Sudah di-hash di RegisterController
                'no_telepon_verified_at' => Carbon::now(),
            ]);

            // Login otomatis
            Auth::login($user);

            // Bersihkan session
            Session::forget(['temp_registration_data', 'otp_code', 'otp_expires_at', 'target_phone']);

            return redirect('/')->with('success', 'Akun berhasil dibuat dan nomor WhatsApp terverifikasi!');
        }

        // FLOW 2: Verifikasi Existing User (sudah login)
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('register')->with('error', 'Silakan login terlebih dahulu.');
        }

        $sessionOtp = session('otp_code');
        $expiresAt = session('otp_expires_at');

        if (!$sessionOtp) {
            return back()->with('error', 'Sesi OTP tidak valid. Silakan minta kode baru.');
        }

        if ($expiresAt && now()->greaterThan($expiresAt)) {
            return back()->with('error', 'Kode OTP sudah kadaluarsa. Silakan minta kode baru.');
        }

        if ($request->otp == $sessionOtp) {
            $user->update(['no_telepon_verified_at' => Carbon::now()]);
            session()->forget(['otp_code', 'otp_expires_at', 'target_phone']);

            return redirect('/')->with('success', 'Nomor WhatsApp berhasil diverifikasi! Anda sekarang bisa Booking.');
        }

        return back()->with('error', 'Kode OTP salah. Silakan coba lagi.');
    }

    public function resend()
    {
        $waService = new WhatsAppService();
        $otp = rand(100000, 999999);
        $expiresAt = now()->addMinutes(2);

        // FLOW 1: Registrasi baru
        if (Session::has('temp_registration_data')) {
            $phone = Session::get('temp_registration_data')['no_telepon'];
            $waService->sendOTP($phone, $otp);
            
            Session::put('otp_code', $otp);
            Session::put('otp_expires_at', $expiresAt);
            Session::put('target_phone', $phone);
            
            return back()->with('success', 'Kode OTP baru telah dikirim ke WhatsApp Anda.');
        }

        // FLOW 2: User sudah login
        $user = Auth::user();
        if ($user) {
            $waService->sendOTP($user->no_telepon, $otp);
            
            Session::put('otp_code', $otp);
            Session::put('otp_expires_at', $expiresAt);
            Session::put('target_phone', $user->no_telepon);
            
            return back()->with('success', 'Kode OTP baru telah dikirim ke WhatsApp Anda.');
        }

        return redirect()->route('register')->with('error', 'Silakan daftar terlebih dahulu.');
    }
}