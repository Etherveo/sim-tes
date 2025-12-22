<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use Illuminate\Support\Facades\Notification;

class KeranjangController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('keranjang.index', compact('products'));
    }

public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'integer|min:1',
            'pickup_date' => 'required|date|after_or_equal:today',
            'pickup_time' => 'required', 
        ]);

        $pickupDate = $request->input('pickup_date');
        $pickupTime = $request->input('pickup_time');
        $cartItems = $request->input('items');
        $productIds = array_keys($cartItems);
        $products = Product::whereIn('id', $productIds)->get();

        if ($products->isEmpty()) {
            return response()->json(['message' => 'Keranjang kosong.'], 400);
        }

        DB::beginTransaction();

        try {
            $totalPrice = 0;
            $orderItemsData = [];

            foreach ($products as $product) {
                $qty = $cartItems[$product->id];
                
                if ($qty > $product->stok) {
                    throw new \Exception("Stok untuk {$product->nama_produk} tidak mencukupi.");
                }

                $price = ($product->harga_diskon && $product->harga_diskon < $product->harga) 
                            ? $product->harga_diskon 
                            : $product->harga;

                $subtotal = $price * $qty;
                $totalPrice += $subtotal;

                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $price,
                ];
                
                $product->decrement('stok', $qty);
            }

            $order = Order::create([
                'user_id' => Auth::id(),
                'total_price' => $totalPrice,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'pickup_date' => $pickupDate, 
                'pickup_time' => $pickupTime, 
                'alamat_pengiriman' => 'Ambil di Toko', 
            ]);

            foreach ($orderItemsData as $item) {
                $order->items()->create($item);
            }

            DB::commit();

            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new NewOrderNotification($order));

            return response()->json([
                'success' => true, 
                'message' => 'Pesanan berhasil dibuat! Silakan datang pada waktu yang ditentukan.',
                'order_id' => $order->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}