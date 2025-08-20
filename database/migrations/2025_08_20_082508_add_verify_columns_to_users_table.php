<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan perubahan (menambah kolom).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('verify_key', 10)->nullable()->after('password')->index();
            $table->timestamp('expired_key')->nullable()->after('verify_key');
        });
    }

    /**
     * Rollback perubahan (hapus kolom).
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['verify_key', 'expired_key']);
        });
    }
};
