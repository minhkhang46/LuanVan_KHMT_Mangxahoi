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
            $table->text('description')->nullable(); // Thêm cột mô tả
            $table->string('cv')->nullable();        // Thêm cột CV để lưu tên file
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
            $table->dropColumn('description');
            $table->dropColumn('cv');
        });
    }
};
