<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Category;

class KatalogController extends Controller
{

    public function index(Request $request)
    {
        $promoProducts = Product::whereNotNull('harga_diskon')
                                ->where('harga_diskon', '<', DB::raw('harga'))
                                ->orderBy('nama_produk', 'asc')
                                ->take(20)
                                ->get();

        $bestSellerProducts = Product::select('products.*', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed') 
            ->groupBy('products.id')
            ->orderByDesc('total_sold')
            ->take(3)
            ->get();

        $allProducts = Product::orderBy('id', 'desc')
            ->take(35)
            ->get();

        return view('welcome', compact('promoProducts', 'allProducts', 'bestSellerProducts'));
    }

    public function showAllProducts(Request $request)
    {
        // LOGIKA SORTING UNTUK SEMUA PRODUK
        $sortBy = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'asc');
        
        $categories = Category::orderBy('nama_kategori', 'asc')->get(); // Buat list di modal
        $query = Product::query();

        $validSorts = [
            'nama'  => 'nama_produk',
            'harga' => 'harga',
            'kode'  => 'id'
        ];

        $column = $validSorts[$sortBy] ?? 'nama_produk';
        $direction = in_array($sortDirection, ['asc', 'desc']) ? $sortDirection : 'asc';

        // LOGIKA FILTER (Sama kayak Admin tapi disesuaikan)
        if ($request->filled('filter_kategori')) {
            $query->where('kategori_produk', $request->filter_kategori);
        }

        if ($request->filter_diskon == '1') {
            $query->whereNotNull('harga_diskon')
                ->whereColumn('harga_diskon', '<', 'harga');
        }

        if ($request->filled('filter_harga_min')) {
            $query->where('harga', '>=', $request->filter_harga_min);
        }

        if ($request->filled('filter_harga_max')) {
            $query->where('harga', '<=', $request->filter_harga_max);
        }

        // Gunakan paginate() untuk efisiensi database
        $allProducts = $query->orderBy($column, $direction)->paginate(45);

        return view('produk-semua', compact('allProducts', 'categories'));
    }

    public function showAllPromo(Request $request)
    {
        $sortBy = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'asc');

        $categories = Category::orderBy('nama_kategori', 'asc')->get();

        $query = Product::whereNotNull('harga_diskon')
            ->where('harga_diskon', '<', DB::raw('harga'));

        $validSorts = [
            'nama'  => 'nama_produk',
            'harga' => 'harga',
            'kode'  => 'id'
        ];
            
        $column = $validSorts[$sortBy] ?? 'nama_produk';
        $direction = in_array($sortDirection, ['asc', 'desc']) ? $sortDirection : 'asc';
        
        if ($request->filled('filter_kategori')) {
            $query->where('kategori_produk', $request->filter_kategori);
        }
            
        if ($request->filter_diskon == '1') {
            $query->whereNotNull('harga_diskon')
                ->whereColumn('harga_diskon', '<', 'harga');
        }
            
        if ($request->filled('filter_harga_min')) {
            $query->where('harga', '>=', $request->filter_harga_min);
        }
            
        if ($request->filled('filter_harga_max')) {
            $query->where('harga', '<=', $request->filter_harga_max);
        }

        // Sama kayak all product
        $promoProducts = $query->orderBy($column, $direction)->paginate(30);

        return view('produk-promo', compact('promoProducts', 'categories'));
    }

    public function showCategoryGrid(Request $request)
    {
        $query = Category::query();

        if ($search = $request->input('search')) {
            $query->where('nama_kategori', 'LIKE', "%{$search}%");
        }

        $categories = $query->orderBy('nama_kategori', 'asc')->get();
        
        return view('kategori', compact('categories'));
    }

    public function showProductsByCategory(Request $request, $kategori_slug)
    {
        $kategoriNama = Str::of($kategori_slug)->replace('-', ' ')->title();
        
        $query = Product::where('kategori_produk', $kategoriNama);

        $promoProducts = (clone $query)
                            ->whereNotNull('harga_diskon')
                            ->where('harga_diskon', '<', DB::raw('harga'))
                            ->orderBy('nama_produk', 'asc')
                            ->get();

        // LOGIKA SORTING
        $sortBy = $request->input('sort_by', 'nama');
        $sortDirection = $request->input('sort_direction', 'asc');
        
        $validSorts = [
            'nama'  => 'nama_produk',
            'harga' => 'harga',
            'kode'  => 'id'
        ];

        $column = $validSorts[$sortBy] ?? 'nama_produk';
        $direction = in_array($sortDirection, ['asc', 'desc']) ? $sortDirection : 'asc';

        $categoryProducts = $query->orderBy($column, $direction)->paginate(30);

        return view('produk-by-kategori', compact('kategoriNama', 'promoProducts', 'categoryProducts'));
    }

    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:1',
        ]);

        $searchTerm = $request->input('search');
        $categories = Category::orderBy('nama_kategori', 'asc')->get(); // Buat list di modal

        $query = Product::query();

        // --- 1. LOGIKA SEARCH DASAR ---
        $query->where(function($q) use ($searchTerm) {
            $q->where('nama_produk', 'LIKE', "%{$searchTerm}%")
            ->orWhere('deskripsi', 'LIKE', "%{$searchTerm}%")
            ->orWhere('kategori_produk', 'LIKE', "%{$searchTerm}%");
        });

        // --- 2. LOGIKA FILTER (Sama kayak Admin tapi disesuaikan) ---
        if ($request->filled('filter_kategori')) {
            $query->where('kategori_produk', $request->filter_kategori);
        }

        if ($request->filter_diskon == '1') {
            $query->whereNotNull('harga_diskon')
                ->whereColumn('harga_diskon', '<', 'harga');
        }

        if ($request->filled('filter_harga_min')) {
            $query->where('harga', '>=', $request->filter_harga_min);
        }

        if ($request->filled('filter_harga_max')) {
            $query->where('harga', '<=', $request->filter_harga_max);
        }

        // --- 3. LOGIKA SORTING ---
        $sortBy = $request->input('sort_by', 'nama');
        $sortDirection = $request->input('sort_direction', 'asc');
        $validSorts = ['nama' => 'nama_produk', 'harga' => 'harga', 'kode' => 'id'];
        $column = $validSorts[$sortBy] ?? 'nama_produk';
        $direction = in_array($sortDirection, ['asc', 'desc']) ? $sortDirection : 'asc';

        $products = $query->orderBy($column, $direction)->get();

        return view('produk-cari', [
            'products'   => $products,
            'query'      => $searchTerm,
            'categories' => $categories
        ]);
    }
}