<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class UserOrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items.product')
                       ->where('user_id', Auth::id())
                       ->latest()
                       ->get();

        return view('riwayat.index', compact('orders'));
    }
}