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
            $table->string('name', 255)->nullable(false);
            $table->string('description', 255)->nullable(false);
            $table->boolean('is_double_client')->nullable(false)->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('deeds');
    }
};
