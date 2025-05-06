<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_code')->unique()->nullable(); // Tạo cột order_code, có thể là null và duy nhất
        });
    }
    
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('order_code'); // Xóa cột nếu cần rollback
        });
    }
    
};
