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
            $table->string('tracking_code');

            $table->timestamps();


            $table->foreign('deed_id')->references('id')->on('deeds')->cascadeOnUpdate();
            $table->foreign('user_notaris_id')->references('id')->on('users')->cascadeOnUpdate();
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity');
    }
};
