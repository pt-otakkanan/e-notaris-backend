<?php
// database/migrations/2025_09_22_000001_add_ttd_columns_to_draft_deeds.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('draft_deeds', function (Blueprint $table) {
            $table->string('file_ttd')->nullable()->after('file_path');
            $table->string('file_ttd_path')->nullable()->after('file_ttd');
        });
    }

    public function down(): void
    {
        Schema::table('draft_deeds', function (Blueprint $table) {
            $table->dropColumn(['file_ttd', 'file_ttd_path']);
        });
    }
};
