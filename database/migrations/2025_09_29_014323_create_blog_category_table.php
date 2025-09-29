<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blog_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')
                ->constrained('blogs')
                ->cascadeOnDelete();
            $table->foreignId('category_blog_id')
                ->constrained('category_blogs')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['blog_id', 'category_blog_id']); // no dupe
            $table->index('category_blog_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_category');
    }
};
