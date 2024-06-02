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
            $table->unsignedBigInteger('project_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('status'); // trạng thái: 0 - mới, 1 - đang làm, 2 - hoàn thành
            $table->date('due_date'); // ngày hoàn thành dự kiến
            $table->unsignedBigInteger('create_by');
            $table->unsignedBigInteger('assigned_user_id');
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('create_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
