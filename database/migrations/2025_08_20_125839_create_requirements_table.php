<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('requirements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activity_id');
            $table->string('name', 255)->nullable(false);
            $table->boolean('is_file')->nullable(false)->default(false);
            $table->timestamps();

            $table->foreign('activity_id')->references('id')->on('activity')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('requirements');
    }
};
