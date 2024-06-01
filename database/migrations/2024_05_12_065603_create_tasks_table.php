<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('status');
            $table->boolean('is_deadline');
            $table->date('done_deadline')->nullable();
            $table->unsignedBigInteger('create_by');
            $table->unsignedBigInteger('assigned_user_id');
            $table->timestamps();

            $table->foreign('create_by')->references('id')->on('users')->onDelete('cascade'); // Khóa ngoại cho create_by
            $table->foreign('assigned_user_id')->references('id')->on('users')->onDelete('cascade'); // Khóa ngoại cho assigned_user_id
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
