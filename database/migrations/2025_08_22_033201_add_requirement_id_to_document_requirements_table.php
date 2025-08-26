<?php
// database/migrations/2025_08_22_000001_add_requirement_columns_to_document_requirements.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('document_requirements', function (Blueprint $table) {
            $table->unsignedBigInteger('requirement_id')->nullable()->after('user_id');
            $table->foreign('requirement_id')->references('id')->on('requirements')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('document_requirements', function (Blueprint $table) {
            // kalau ada FK, drop FK dulu
            $table->dropForeign(['requirement_id']);
        });
    }
};
