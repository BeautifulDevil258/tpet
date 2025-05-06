<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade'); // Khóa ngoại tới bảng orders
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Khóa ngoại tới bảng products
            $table->integer('quantity'); // Số lượng sản phẩm
            $table->decimal('price', 10, 2); // Giá đơn vị sản phẩm
            $table->timestamps(); // Thời gian tạo và cập nhật thông tin sản phẩm trong đơn hàng
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
