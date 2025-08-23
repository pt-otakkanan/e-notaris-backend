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
        Schema::table('document_requirements', function (Blueprint $table) {
            $table->string('requirement_name')->nullable()->after('requirement_id');
            $table->boolean('is_file_snapshot')->default(false)->after('requirement_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_requirements', function (Blueprint $table) {
            $table->dropColumn(['requirement_name', 'is_file_snapshot']);
        });
    }
};
