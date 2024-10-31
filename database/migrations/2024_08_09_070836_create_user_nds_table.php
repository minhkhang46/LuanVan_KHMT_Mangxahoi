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
        Schema::create('user_nds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('emailorphone')->unique();
            $table->string('gender');
            $table->string('password');
            $table->string('date');
            $table->string('avatar'); // Changed 'Avatar' to 'avatar'
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
        Schema::dropIfExists('user_nd');
    }
};
