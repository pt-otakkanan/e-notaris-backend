<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('draft_deeds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activity_id');

            $table->text('custom_value_template')->nullable(false);
            $table->timestamp('reading_schedule')->nullable();
            $table->enum('status_approval', ['pending', 'approved', 'rejected'])->nullable(false);
            $table->string('file', 255)->nullable(false);
            $table->string('file_path', 255)->nullable(false);
            $table->timestamps();

            $table->foreign('activity_id')->references('id')->on('activity')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('draft_deeds');
    }
};
