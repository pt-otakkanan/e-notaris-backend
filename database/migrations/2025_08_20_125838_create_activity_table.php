<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('activity', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('deed_id');
            $table->unsignedBigInteger('user_notaris_id');     // FK -> users.id
            $table->unsignedBigInteger('activity_notaris_id'); // tidak ada ref di spec; biarkan tanpa FK
            $table->unsignedBigInteger('first_client_id');     // FK -> users.id
            $table->unsignedBigInteger('second_client_id')->nullable(); // nullable sesuai spec

            $table->enum('status_approval', ['pending', 'approved', 'rejected']);
            $table->enum('first_client_approval', ['pending', 'approved', 'rejected']);
            $table->enum('second_client_approval', ['pending', 'approved', 'rejected']);
            $table->string('tracking_code', 255);
            $table->timestamps();

            // FK (perhatikan NAMA kolomnya benar)
            $table->foreign('deed_id')->references('id')->on('deeds')->cascadeOnUpdate();
            $table->foreign('user_notaris_id')->references('id')->on('users')->cascadeOnUpdate();
            $table->foreign('first_client_id')->references('id')->on('users')->cascadeOnUpdate();
            $table->foreign('second_client_id')->references('id')->on('users')->nullOnDelete()->cascadeOnUpdate();

            // index untuk pencarian tracking
            $table->index('tracking_code');
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity');
    }
};
