<?php
// database/migrations/2025_10_01_000000_create_settings_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            // Media
            $table->string('logo', 2048)->nullable();
            $table->string('logo_path')->nullable();       // public_id Cloudinary
            $table->string('favicon', 2048)->nullable();
            $table->string('favicon_path')->nullable();    // public_id Cloudinary

            // Kontak & Sosial
            $table->string('telepon', 50)->nullable();
            $table->string('facebook', 2048)->nullable();
            $table->string('instagram', 2048)->nullable();
            $table->string('twitter', 2048)->nullable();
            $table->string('linkedin', 2048)->nullable();

            // Konten
            $table->string('title_hero', 200)->nullable();
            $table->text('desc_hero')->nullable();
            $table->text('desc_footer')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
