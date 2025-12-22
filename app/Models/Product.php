<?php

namespace App\Models; 

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// DAN INI YANG PENTING: class Product extends Model
class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
     // Ini adalah bidang dari form Anda yang boleh diisi
    protected $fillable = [
        'nama_produk',
        'kategori_produk',
        'harga',
        'harga_diskon',
        'gambar',
        'deskripsi',
        'stok',
    ];
}