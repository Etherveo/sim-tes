<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Notifications\OrderStatusUpdated; // <-- WAJIB ADA: Import Notifikasi

class OrderController extends Controller
{
    /**
     * Menampilkan daftar pesanan dengan Filter & Search.
     */
    public function index(Request $request)
    {
        // 1. Mulai Query
        $query = Order::with(['user', 'items']);

        // 2. Logika PENCARIAN (Search)
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                // Cari berdasarkan ID Order
                $q->where('id', 'like', "%{$search}%")
                  // Cari berdasarkan Nama User (Relasi)
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('nama_lengkap', 'like', "%{$search}%");
                  });
            });
        }

        // 3. Logika FILTER STATUS
        if ($status = $request->input('filter_status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        // 4. Logika PENGURUTAN (Sorting)
        $sortBy = $request->input('sort_by');

        if ($sortBy == 'pickup_closest') {
            $query->orderBy('pickup_date', 'asc')
                  ->orderBy('pickup_time', 'asc');
        } else {
            // Default: Selesai di bawah
            $query->orderByRaw("CASE WHEN status = 'completed' THEN 1 ELSE 0 END ASC")
                  ->latest(); 
        }

        // Eksekusi Query
        $orders = $query->get();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update Status & Kirim Notifikasi
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:ready,completed',
        ]);

        // 1. Update Status
        $order->update(['status' => $request->status]);

        // 2. KIRIM NOTIFIKASI KE USER (Pastikan user masih ada)
        if ($order->user) {
            $order->user->notify(new OrderStatusUpdated($order));
        }

        // 3. Pesan Sukses untuk Admin
        $message = $request->status == 'ready' 
            ? 'Pesanan ditandai SIAP DIAMBIL.' 
            : 'Pesanan telah SELESAI.';

        return redirect()->back()->with('success', $message);
    }
}