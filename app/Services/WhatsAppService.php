<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Kirim pesan OTP menggunakan Fonnte
     * @param string 
     * @param string 
     */
   public function sendOTP($target, $otp)
    {
        $token = env('FONNTE_TOKEN');
        $message = "Kode OTP untuk masuk di irastatioery Anda adalah: *{$otp}*.\n\nJangan berikan kode ini kepada siapapun.\n(Dikirim via Fonnte)";

        try {
            if (!$token) {
                Log::error('Fonnte token tidak ditemukan di env (FONNTE_TOKEN).');
                return false;
            }

            // Pastikan format header Authorization: tambahkan "Bearer " jika belum ada
            $authHeader = $token;
            if (stripos($token, 'bearer') === false) {
                $authHeader = 'Bearer ' . $token;
            }

            // Normalisasi nomor target agar sesuai kebanyakan API WA (Fonnte biasanya butuh nomor tanpa leading 0)
            $raw = preg_replace('/[^0-9\+]/', '', (string) $target);
            if (strpos($raw, '+') === 0) {
                $raw = substr($raw, 1);
            }
            if (strpos($raw, '62') === 0) {
                $normalized = substr($raw, 2);
            } elseif (strpos($raw, '0') === 0) {
                $normalized = ltrim($raw, '0');
            } else {
                $normalized = $raw;
            }

            $payload = [
                'target' => $normalized,
                'message' => $message,
                'countryCode' => '62',
            ];

            Log::info('Fonnte request payload: ' . json_encode($payload));

            // Gunakan tanpa verifikasi SSL (opsional, jika ada masalah lokal)
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => $authHeader,
                    'Accept' => 'application/json',
                ])->post('https://api.fonnte.com/send', $payload);

            $body = null;
            try {
                $body = $response->json();
            } catch (\Throwable $t) {
                $body = ['raw' => $response->body()];
            }

            // Jika API menolak token (pesan "invalid token"), coba lagi tanpa prefix "Bearer "
            $needRetryWithoutBearer = false;
            if (is_array($body) && isset($body['reason']) && stripos($body['reason'], 'invalid token') !== false) {
                $needRetryWithoutBearer = true;
            }

            if ($needRetryWithoutBearer) {
                Log::warning('Fonnte indicated invalid token with Authorization header: trying raw token header and retrying.');
                $retryResponse = Http::withoutVerifying()
                    ->withHeaders([
                        'Authorization' => $token,
                        'Accept' => 'application/json',
                    ])->post('https://api.fonnte.com/send', $payload);

                try {
                    $retryBody = $retryResponse->json();
                } catch (\Throwable $t) {
                    $retryBody = ['raw' => $retryResponse->body()];
                }

                Log::info('Fonnte retry response: ' . json_encode($retryBody));

                if ($retryResponse->successful()) {
                    Log::info("OTP Fonnte Berhasil dikirim ke: " . $target . ' | retry response: ' . $retryResponse->body());
                    return true;
                }

                Log::error("Gagal kirim Fonnte (retry) ke {$target}. Status: {$retryResponse->status()} Response: {$retryResponse->body()}");
                return false;
            }

            if ($response->successful()) {
                Log::info("OTP Fonnte Berhasil dikirim ke: " . $target . ' | response: ' . $response->body());
                return true;
            } else {
                Log::error("Gagal kirim Fonnte ke {$target}. Status: {$response->status()} Response: {$response->body()}");
                return false;
            }

        } catch (\Exception $e) {
            Log::error("Error Koneksi Fonnte: " . $e->getMessage());
            return false;
        }
    }
}