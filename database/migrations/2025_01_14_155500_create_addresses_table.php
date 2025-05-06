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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Liên kết với bảng users
            $table->string('street'); // Địa chỉ đường phố
            $table->string('city');   // Thành phố
            $table->string('province')->nullable(); // Tỉnh, nếu cần
            $table->string('postal_code')->nullable(); // Mã bưu chính, nếu cần
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('addresses');
    }
    
};
