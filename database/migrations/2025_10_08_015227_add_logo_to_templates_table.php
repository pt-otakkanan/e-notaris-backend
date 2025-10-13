<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('file');       // URL Cloudinary
            $table->string('logo_path')->nullable()->after('logo');  // public_id Cloudinary
        });
    }

    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->dropColumn(['logo', 'logo_path']);
        });
    }
};
