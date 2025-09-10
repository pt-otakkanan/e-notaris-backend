<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE `draft_deeds` MODIFY `file` VARCHAR(255) NULL");
        DB::statement("ALTER TABLE `draft_deeds` MODIFY `file_path` VARCHAR(255) NULL");
        DB::statement("ALTER TABLE `draft_deeds` MODIFY `custom_value_template` LONGTEXT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `draft_deeds` MODIFY `file` VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE `draft_deeds` MODIFY `file_path` VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE `draft_deeds` MODIFY `custom_value_template` TEXT NOT NULL");
    }
};
