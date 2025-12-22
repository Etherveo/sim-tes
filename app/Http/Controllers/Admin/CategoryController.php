<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product; // <-- PENTING: Tambahkan ini agar bisa update produk
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:categories',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $path = null;
        if ($request->hasFile('gambar')) {
            $fileName = time() . '_' . $request->file('gambar')->getClientOriginalName();
            $path = $request->file('gambar')->store('categories', 'public');
        }

        Category::create([
            'nama_kategori' => $request->nama_kategori,
            'slug' => Str::slug($request->nama_kategori),
            'gambar' => $path,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * MODIFIKASI: Update Kategori + Update Produk Terkait
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:categories,nama_kategori,' . $category->id,
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // 1. Simpan nama lama sebelum diubah
        $oldCategoryName = $category->nama_kategori;
        $newCategoryName = $request->nama_kategori;

        $data = [
            'nama_kategori' => $newCategoryName,
            'slug' => Str::slug($newCategoryName),
        ];

        if ($request->hasFile('gambar')) {
            if ($category->gambar) {
                Storage::disk('public')->delete($category->gambar);
            }
            $fileName = time() . '_' . $request->file('gambar')->getClientOriginalName();
            $path = $request->file('gambar')->store('categories', 'public');
            $data['gambar'] = $path;
        }

        // 2. Update Data Kategori di tabel 'categories'
        $category->update($data);

        // 3. LOGIKA BARU: Sinkronisasi Produk
        // Jika nama kategori berubah, update juga semua produk yang pakai nama lama
        if ($oldCategoryName !== $newCategoryName) {
            Product::where('kategori_produk', $oldCategoryName)
                   ->update(['kategori_produk' => $newCategoryName]);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Kategori (dan produk terkait) berhasil diperbarui!');
    }

    public function destroy(Category $category)
    {
        if ($category->gambar) {
            Storage::disk('public')->delete($category->gambar);
        }
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus!');
    }
}