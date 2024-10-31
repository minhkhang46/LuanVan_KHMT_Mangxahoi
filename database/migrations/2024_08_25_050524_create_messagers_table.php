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
        Schema::create('messagers', function (Blueprint $table) {
            $table->id(); // Cột ID tự động tăng
            $table->integer('sender_id')->constrained('user_nds')->onDelete('cascade'); // Cột người gửi
            $table->integer('receiver_id')->constrained('user_nds')->onDelete('cascade'); // Cột người nhận
            $table->text('content'); // Nội dung tin nhắn
            $table->boolean('is_read')->default(0); // Trạng thái đọc tin nhắn
            $table->timestamps(); // Cột timestamps (created_at, updated_at)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messagers');
    }
};
