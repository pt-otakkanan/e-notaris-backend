<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete(); // kalau user dihapus, blog ikut terhapus
            $table->string('image')->nullable();      // URL (Cloudinary secure URL)
            $table->string('image_path')->nullable(); // public_id Cloudinary
            $table->string('title', 200);
            $table->longText('description');          // konten HTML dari Quill
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
