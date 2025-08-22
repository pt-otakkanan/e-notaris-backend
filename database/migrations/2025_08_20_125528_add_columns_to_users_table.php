<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambah kolom baru
            $table->string('telepon', 255)->nullable()->after('email');
            $table->string('gender', 255)->nullable()->after('telepon');
            $table->string('address', 255)->nullable()->after('password');
            $table->enum('status_verification', ['pending', 'approved', 'rejected'])
                ->default('pending')
                ->after('address');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'telepon',
                'gender',
                'address',
                'status_verification'
            ]);
        });
    }
};
