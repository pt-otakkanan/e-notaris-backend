<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('document_requirements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activity_notaris_id');
            $table->unsignedBigInteger('user_id');


            $table->string('file', 255)->nullable(false);
            $table->string('file_path', 255)->nullable(false);
            $table->enum('status_approval', ['pending', 'approved', 'rejected'])->nullable(false);
            $table->timestamps();

            $table->foreign('activity_notaris_id')->references('id')->on('activity')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_requirements');
    }
};
