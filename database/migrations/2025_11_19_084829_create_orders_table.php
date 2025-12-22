<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            // Menghubungkan pesanan dengan pengguna
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->decimal('total_price', 12, 2);
            $table->string('status')->default('pending'); // pending, paid, shipped, completed, cancelled
            $table->string('payment_status')->default('unpaid');
            
            // Informasi pengiriman sederhana
            $table->text('alamat_pengiriman')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};