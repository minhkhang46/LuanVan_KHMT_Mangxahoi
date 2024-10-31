<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropForeignKeyFromNotificationsTable extends Migration
{
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Xóa khóa ngoại
            $table->dropForeign(['user_id']);
        });
    }

    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Khôi phục khóa ngoại nếu cần
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
}
