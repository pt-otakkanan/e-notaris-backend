<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activity_id');

            $table->date('date')->nullable(false);
            $table->time('time')->nullable(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('activity_id')->references('id')->on('activity')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('schedules');
    }
};
