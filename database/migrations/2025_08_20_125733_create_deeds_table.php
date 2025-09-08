<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('deeds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_notaris_id');
            $table->string('name', 255)->nullable(false);
            $table->string('description', 255)->nullable(false);
            $table->timestamps();

            $table->foreign('user_notaris_id')->references('id')->on('users')->cascadeOnUpdate();
        });
    }

    public function down()
    {
        Schema::dropIfExists('deeds');
    }
};
