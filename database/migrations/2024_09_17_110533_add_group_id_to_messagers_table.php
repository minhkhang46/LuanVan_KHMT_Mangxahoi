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
           
                $table->integer('group_id')->nullable()->after('receiver_id');
               
           
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
            $table->integer('group_id');
        });
    }
};
