<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil admin.
     */
    public function show()
    {
        // 1. Ambil data pengguna yang sedang login
        $user = Auth::user();

        // 2. Kirim data tersebut ke view
        return view('admin.profil.show', compact('user'));
    }
}