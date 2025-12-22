<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $unreadNotifications = Auth::user()->unreadNotifications()->get();
        
        $readNotifications = Auth::user()->readNotifications()->limit(20)->get();
        
        return view('admin.notifications.index', compact('unreadNotifications', 'readNotifications'));
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
            
            // Redirect ke detail pesanan (jika kita sudah buat halaman detailnya nanti)
            // Untuk sekarang kita redirect kembali ke daftar notifikasi atau pesanan
            return redirect()->route('admin.orders.index'); 
        }

        return back();
    }
    
    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Semua notifikasi telah ditandai sudah dibaca.');
    }
}