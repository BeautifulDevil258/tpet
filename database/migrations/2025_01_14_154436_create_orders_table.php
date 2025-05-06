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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Khóa ngoại tới bảng users
            $table->string('status')->default('pending'); // Trạng thái đơn hàng (pending, completed, cancelled, etc.)
            $table->decimal('total_price', 10, 2); // Tổng giá trị đơn hàng
            $table->text('shipping_address'); // Địa chỉ giao hàng
            $table->string('payment_method'); // Phương thức thanh toán
            $table->timestamps(); // Thời gian tạo và cập nhật đơn hàng
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
