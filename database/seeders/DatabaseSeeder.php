<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;


    public function run(): void
    {
        User::create([
            'nama_lengkap'  => 'Administrator Utama',
            'username'      => 'admin',
            'email'         => 'admin@irastationery.com',
            'jenis_kelamin' => 'Laki-laki', 
            'password'      => Hash::make('password123'), 
            'role'          => 'admin', 
        ]);
    }
}
