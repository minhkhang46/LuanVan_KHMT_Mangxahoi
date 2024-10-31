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
        Schema::table('user_nds', function (Blueprint $table) {
            $table->string('chuyende')->nullable(); // Thêm cột chuyền đề, nullable nếu không bắt buộc
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_nds', function (Blueprint $table) {
            $table->dropColumn('chuyende'); // Xóa cột chuyền đề khi rollback
        });
    }
};
