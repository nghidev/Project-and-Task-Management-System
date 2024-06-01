<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitUserTable extends Migration
{
    public function up()
    {
        Schema::create('unit_user', function (Blueprint $table) {
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->primary(['unit_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('unit_user');
    }
}
