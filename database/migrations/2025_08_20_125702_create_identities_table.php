<?php

// 4. Migration: 2024_08_20_000004_create_identities_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('identities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('ktp', 255);
            $table->string('file_ktp', 255);
            $table->string('file_ktp_path', 255);
            $table->string('file_kk', 255);
            $table->string('file_kk_path', 255);
            $table->string('npwp', 255)->nullable();
            $table->string('file_npwp', 255)->nullable();
            $table->string('file_npwp_path', 255)->nullable();
            $table->string('ktp_notaris', 255)->nullable();
            $table->string('file_ktp_notaris', 255)->nullable();
            $table->string('file_ktp_notaris_path', 255)->nullable();
            $table->string('file_sign', 255);
            $table->string('file_sign_path', 255);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('identities');
    }
};
