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
        Schema::dropIfExists('group_members');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Liên kết với bảng users
        $table->foreignId('group_id')->constrained()->onDelete('cascade'); // Liên kết với bảng posts
    }
};
