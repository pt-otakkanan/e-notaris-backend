<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('document_requirements', function (Blueprint $table) {
            // kolom referensi ke template, nullable agar perubahan tidak memecah data yang ada
            $table->unsignedBigInteger('deed_requirement_template_id')->nullable()->after('id');

            // index & FK
            $table->index('deed_requirement_template_id');
            $table->foreign('deed_requirement_template_id')
                ->references('id')
                ->on('deed_requirement_templates')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down()
    {
        Schema::table('document_requirements', function (Blueprint $table) {
            $table->dropForeign(['deed_requirement_template_id']);
            $table->dropIndex(['deed_requirement_template_id']);
            $table->dropColumn('deed_requirement_template_id');
        });
    }
};
