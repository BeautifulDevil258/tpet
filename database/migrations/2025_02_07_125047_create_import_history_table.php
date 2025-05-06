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
        Schema::create('import_history', function (Blueprint $table) {
            $table->id(); // ID tự tăng
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // Khóa ngoại tới bảng products
            $table->decimal('import_price', 10, 2); // Giá nhập
            $table->integer('quantity'); // Số lượng nhập
            $table->date('import_date'); // Ngày nhập hàng
            $table->timestamps(); // Tự động tạo created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_history');
    }
};
