<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->longText('custom_value')->nullable();
            $table->timestamps(); // boleh dihapus kalau tidak perlu
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
