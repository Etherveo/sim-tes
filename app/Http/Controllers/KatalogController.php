<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Category;

class KatalogController extends Controller
{
    public function index()
    {
        $promoProducts = Product::whereNotNull('harga_diskon')
                                ->where('harga_diskon', '<', DB::raw('harga'))
                                ->orderBy('nama_produk', 'asc')
                                ->get();

        $bestSellerProducts = Product::select('products.*', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed') 
            ->groupBy('products.id')
            ->orderByDesc('total_sold') 
            ->take(3) 
            ->get();

        $allProducts = Product::orderBy('nama_produk', 'asc')->get();

        return view('welcome', compact('promoProducts', 'allProducts', 'bestSellerProducts'));
    }

    public function showCategoryGrid()
    {
        $categories = Category::orderBy('nama_kategori', 'asc')->get();
        return view('kategori', compact('categories'));
    }

    public function showProductsByCategory($kategori_slug)
    {
        $kategoriNama = Str::of($kategori_slug)->replace('-', ' ')->title();
        $categoryQuery = Product::where('kategori_produk', $kategoriNama);

        $promoProducts = (clone $categoryQuery)
                            ->whereNotNull('harga_diskon')
                            ->where('harga_diskon', '<', DB::raw('harga'))
                            ->orderBy('nama_produk', 'asc')
                            ->get();

        $categoryProducts = $categoryQuery->orderBy('nama_produk', 'asc')->get();

        return view('produk-by-kategori', [
            'kategoriNama'     => $kategoriNama,
            'promoProducts'    => $promoProducts,
            'categoryProducts' => $categoryProducts,
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:1',
        ]);

        $query = $request->input('search');

        $products = Product::where(function($q) use ($query) {
                            $q->where('nama_produk', 'LIKE', "%{$query}%")
                              ->orWhere('deskripsi', 'LIKE', "%{$query}%")
                              ->orWhere('kategori_produk', 'LIKE', "%{$query}%");
                        })
                        ->orderBy('nama_produk', 'asc')
                        ->get();

        return view('produk-cari', [
            'products' => $products,
            'query'    => $query,
        ]);
    }
}