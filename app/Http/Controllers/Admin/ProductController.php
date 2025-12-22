<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;

class ProductController extends Controller
{
    // ... (method index() tetap sama) ...
    public function index(Request $request)
    {
        $searchQuery = $request->input('search');
        $query = Product::query();

        if ($searchQuery) {
            $query->where(function($q) use ($searchQuery) {
                $q->where('nama_produk', 'LIKE', "%{$searchQuery}%")
                  ->orWhere('kategori_produk', 'LIKE', "%{$searchQuery}%");
                if (is_numeric($searchQuery)) {
                    $q->orWhere('id', $searchQuery);
                }
            });
        }
        $products = $query->orderBy('nama_produk', 'asc')->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('nama_kategori', 'asc')->get();
        return view('admin.products.create', compact('categories'));
    }


    /**
     * Menyimpan produk baru ke database.
     */
    public function store(Request $request)
    {
        // 
        // MODIFIKASI: Mengubah string kosong '' menjadi null
        // 
        if (empty($request->input('harga_diskon'))) {
            $request->merge(['harga_diskon' => null]);
        }

        $validatedData = $request->validate([
            'nama_produk'     => 'required|string|max:255',
            'kategori_produk' => 'required|string',
            'harga'           => 'required|numeric|min:0',
            // Aturan 'nullable' sekarang akan bekerja dengan benar
            'harga_diskon'    => ['nullable', 'numeric', 'min:0', 'lt:harga'],
            'deskripsi'       => 'nullable|string',
            'stok'            => 'required|numeric|min:0', 
            'gambar'          => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'harga_diskon.lt' => 'Harga diskon harus lebih rendah dari harga normal.',
            'stok.numeric' => 'Stok harus berupa angka.',
        ]);

        $path = null;
        if ($request->hasFile('gambar')) {
            $fileName = time() . '_' . $request->file('gambar')->getClientOriginalName();
            $path = $request->file('gambar')->storeAs('products', $fileName, 'public');
        }

        $validatedData['gambar'] = $path;
        Product::create($validatedData);

        return redirect()->route('admin.dashboard')->with('success', 'Produk baru berhasil ditambahkan!');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('nama_kategori', 'asc')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Memperbarui produk di database.
     */
    public function update(Request $request, Product $product)
    {
        // 
        // MODIFIKASI: Mengubah string kosong '' menjadi null
        // 
        if (empty($request->input('harga_diskon'))) {
            $request->merge(['harga_diskon' => null]);
        }
        
        $validatedData = $request->validate([
            'nama_produk'     => 'required|string|max:255',
            'kategori_produk' => 'required|string',
            'harga'           => 'required|numeric|min:0',
            'harga_diskon'    => ['nullable', 'numeric', 'min:0', 'lt:harga'],
            'deskripsi'       => 'nullable|string',
            'stok'            => 'required|numeric|min:0',
            'gambar'          => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'harga_diskon.lt' => 'Harga diskon harus lebih rendah dari harga normal.',
            'stok.numeric' => 'Stok harus berupa angka.',
        ]);

        if ($request->hasFile('gambar')) {
            if ($product->gambar) {
                Storage::disk('public')->delete($product->gambar);
            }
            $fileName = time() . '_' . $request->file('gambar')->getClientOriginalName();
            $path = $request->file('gambar')->storeAs('products', $fileName, 'public');
            $validatedData['gambar'] = $path;
        }

        $product->update($validatedData);

        return redirect()->route('admin.dashboard')->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy(Product $product)
    {
        if ($product->gambar) {
            Storage::disk('public')->delete($product->gambar);
        }
        $product->delete();
        return redirect()->route('admin.dashboard')->with('success', 'Produk berhasil dihapus!');
    }
}