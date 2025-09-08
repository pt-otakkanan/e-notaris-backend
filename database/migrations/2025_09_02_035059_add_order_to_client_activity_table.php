<?php
// database/migrations/2025_09_02_000000_add_order_to_client_activity_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('client_activity', function (Blueprint $table) {
            $table->unsignedSmallInteger('order')
                ->nullable()
                ->after('status_approval');
        });
    }

    public function down(): void
    {
        Schema::table('client_activity', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
