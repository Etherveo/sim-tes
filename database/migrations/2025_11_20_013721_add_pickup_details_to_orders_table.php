<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
        public function up()
        {
            Schema::table('orders', function (Blueprint $table) {
                $table->date('pickup_date')->nullable()->after('total_price');
                $table->time('pickup_time')->nullable()->after('pickup_date');
            });
        }

        public function down()
        {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn(['pickup_date', 'pickup_time']);
            });
        }
};
