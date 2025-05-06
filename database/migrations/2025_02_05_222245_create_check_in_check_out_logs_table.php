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
    Schema::create('check_in_check_out_logs', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('admin_id');
        $table->enum('type', ['check_in', 'check_out']);
        $table->timestamp('original_time')->nullable(); // Thời gian check-in/check-out chính thức
        $table->timestamp('actual_time')->nullable(); // Thời gian thực tế check-in/check-out
        $table->string('reason')->nullable(); // Lý do muộn/sớm
        $table->timestamps();

        $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_in_check_out_logs');
    }
};
