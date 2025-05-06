<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Tiêu đề bài viết
            $table->text('content'); // Nội dung bài viết
            $table->string('image')->nullable(); // Ảnh bài viết
            $table->enum('status', ['draft', 'published'])->default('draft'); // Trạng thái
            $table->unsignedBigInteger('category_id')->nullable(); // Danh mục
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->unsignedBigInteger('user_id'); // Người tạo bài viết
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('posts');
    }
};
