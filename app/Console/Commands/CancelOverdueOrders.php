<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CancelOverdueOrders extends Command
{
    /**
     * Nama perintah yang akan dipanggil oleh Scheduler.
     */
    protected $signature = 'app:cancel-overdue-orders';

    /**
     * Deskripsi perintah.
     */
    protected $description = 'Membatalkan pesanan "Siap Diambil" yang terlambat 1 jam dan mengembalikan stok.';

    /**
     * Eksekusi logika.
     */
    public function handle()
    {
        // 1. Ambil semua pesanan yang statusnya 'ready' (Siap Diambil)
        // Kita gunakan 'with' items dan product agar query efisien
        $orders = Order::where('status', 'ready')
                       ->with('items.product') 
                       ->get();

        $count = 0;

        foreach ($orders as $order) {
            // Gabungkan tanggal dan jam pengambilan menjadi objek Carbon
            // Contoh: 2023-10-27 10:00:00
            try {
                $pickupDateTime = Carbon::parse($order->pickup_date . ' ' . $order->pickup_time);
                
                // Tambah batas toleransi 1 jam
                $deadline = $pickupDateTime->copy()->addHour();
                $this->info("Cek Order #{$order->id}: Deadline {$deadline} vs Sekarang " . now());

                // 2. Cek apakah Waktu Sekarang sudah melewati Deadline
                if (now()->greaterThan($deadline)) {
                    
                    DB::transaction(function () use ($order) {
                        
                        // A. Kembalikan Stok Produk
                        foreach ($order->items as $item) {
                            if ($item->product) {
                                $item->product->increment('stok', $item->quantity);
                            }
                        }

                        // B. Ubah status menjadi 'cancelled'
                        $order->update([
                            'status' => 'cancelled'
                        ]);

                        // Opsional: Anda bisa kirim notifikasi ke user disini bahwa pesanan dibatalkan
                    });

                    $this->info("Pesanan #{$order->id} telah dibatalkan otomatis.");
                    $count++;
                }

            } catch (\Exception $e) {
                $this->error("Gagal memproses Order #{$order->id}: " . $e->getMessage());
            }
        }

        $this->info("Selesai. Total {$count} pesanan dibatalkan.");
    }
}