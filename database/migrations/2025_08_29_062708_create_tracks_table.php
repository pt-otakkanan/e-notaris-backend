<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tracks', function (Blueprint $table) {
            $table->id();

            // Status tiap tahapan flow
            $table->enum('status_invite', ['pending', 'done', 'rejected'])->default('pending');
            $table->enum('status_respond', ['pending', 'done', 'rejected'])->default('pending');
            $table->enum('status_docs', ['pending', 'done', 'rejected'])->default('pending');
            $table->enum('status_draft', ['pending', 'done', 'rejected'])->default('pending');
            $table->enum('status_schedule', ['pending', 'done', 'rejected'])->default('pending');
            $table->enum('status_sign', ['pending', 'done', 'rejected'])->default('pending');
            $table->enum('status_print', ['pending', 'done', 'rejected'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracks');
    }
};
