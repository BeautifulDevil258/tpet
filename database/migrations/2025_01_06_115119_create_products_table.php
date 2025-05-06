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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image'); // Thêm ảnh cho sản phẩm
            $table->decimal('price', 10, 2); // Giá
            $table->integer('quantity'); // Số lượng
            $table->text('description')->nullable(); // Mô tả, có thể null
            $table->decimal('discount_price', 10, 2)->nullable(); // Giá khuyến mại, có thể null
            $table->foreignId('small_category_id')->constrained('small_categories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
