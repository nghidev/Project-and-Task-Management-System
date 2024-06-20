<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUploadedByToAttachmentsTable extends Migration
{
    public function up()
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->unsignedBigInteger('uploaded_by')->after('filetype')->nullable();
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->dropForeign(['uploaded_by']);
            $table->dropColumn('uploaded_by');
        });
    }
}
