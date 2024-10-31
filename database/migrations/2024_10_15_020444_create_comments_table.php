<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->text('content'); // Nội dung bình luận
            $table->integer('user_id'); // Người dùng bình luận
            $table->integer('post_id'); // Bài viết mà bình luận thuộc về
            $table->integer('parent_id')->nullable(); // Bình luận cha (nếu là trả lời)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
};
