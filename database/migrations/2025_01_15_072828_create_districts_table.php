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
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->string('name');  // Tên quận/huyện
            $table->unsignedBigInteger('city_id');  // Khóa ngoại liên kết với bảng cities
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};
