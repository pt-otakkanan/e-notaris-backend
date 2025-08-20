<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('main_value_deeds', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('deed_id');
            $table->text('main_value')->nullable(false);

            $table->timestamps();
            $table->foreign('deed_id')->references('id')->on('deeds')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('main_value_deeds');
    }
};
