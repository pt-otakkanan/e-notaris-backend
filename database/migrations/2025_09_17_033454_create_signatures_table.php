<?php
// database/migrations/2025_09_17_000000_create_signatures_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('signatures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activity_id');
            $table->unsignedBigInteger('draft_deed_id');
            $table->unsignedBigInteger('user_id');

            // file tanda tangan (PNG) di cloud
            $table->string('image_url')->nullable();
            $table->string('image_path')->nullable(); // public_id cloudinary

            // penempatan di PDF (unit: POINT; origin: KIRI-BAWA)
            $table->unsignedInteger('page')->nullable(); // 1-based
            $table->float('x')->nullable();
            $table->float('y')->nullable();
            $table->float('width')->nullable();
            $table->float('height')->nullable();

            // status & audit
            $table->timestamp('signed_at')->nullable(); // saat user confirm/sign
            $table->json('meta')->nullable();           // ip, ua, dll

            $table->timestamps();

            $table->foreign('activity_id')->references('id')->on('activities')->cascadeOnDelete();
            $table->foreign('draft_deed_id')->references('id')->on('draft_deeds')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signatures');
    }
};
