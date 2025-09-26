<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // pakai nama tabel 'blog' (singular) sesuai kebutuhanmu
        Schema::create('blog', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('image')->nullable();      // URL aman Cloudinary
            $table->string('image_path')->nullable(); // public_id Cloudinary
            $table->string('title', 200);
            $table->longText('description');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog');
    }
};
