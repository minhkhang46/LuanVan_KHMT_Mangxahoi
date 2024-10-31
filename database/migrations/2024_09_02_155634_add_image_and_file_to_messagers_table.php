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
        Schema::table('messagers', function (Blueprint $table) {
            $table->string('image')->nullable(); // Thêm cột hình ảnh
            $table->string('file')->nullable();  // Thêm cột file
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('messagers', function (Blueprint $table) {
            $table->dropColumn(['image', 'file']); // Xóa các cột khi rollback
        });
    }
};
