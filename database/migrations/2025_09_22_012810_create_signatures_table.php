<?php
// database/migrations/2025_09_22_000002_create_signatures_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('signatures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('draft_deed_id');
            $table->unsignedBigInteger('activity_id');
            $table->unsignedBigInteger('user_id')->nullable(); // siapa yang nempel (opsional)
            $table->unsignedInteger('page'); // halaman mulai 1
            $table->enum('kind', ['image', 'draw'])->default('image');
            // rasio relatif (0..1) terhadap lebar/tinggi halaman PDF
            $table->decimal('x_ratio', 8, 5);
            $table->decimal('y_ratio', 8, 5);
            $table->decimal('w_ratio', 8, 5);
            $table->decimal('h_ratio', 8, 5);
            // jika kind=draw, simpan data PNG base64 (opsional simpan di storage)
            $table->longText('image_data_url')->nullable();
            // jika ingin cache sumber img (identity.file_sign) yg dibekukan saat itu:
            $table->string('source_image_url')->nullable();

            $table->timestamps();

            $table->foreign('draft_deed_id')->references('id')->on('draft_deeds')->cascadeOnDelete();
            $table->foreign('activity_id')->references('id')->on('activity')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signatures');
    }
};
