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
        Schema::create('client_drafts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('draft_deed_id')->nullable();
            $table->enum('status_approval', ['pending', 'approved', 'rejected'])->default('pending');


            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('draft_deed_id')->references('id')->on('draft_deeds')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_drafts');
    }
};
