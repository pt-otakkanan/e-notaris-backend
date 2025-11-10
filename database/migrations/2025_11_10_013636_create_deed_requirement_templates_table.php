<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('deed_requirement_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deed_id');
            $table->string('name', 255);
            $table->boolean('is_file')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('default_value')->nullable();
            $table->timestamps();

            $table->foreign('deed_id')->references('id')->on('deeds')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('deed_requirement_templates');
    }
};
