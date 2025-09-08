<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $cols = [
        'status_invite',
        'status_respond',
        'status_docs',
        'status_draft',
        'status_schedule',
        'status_sign',
        'status_print',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tambahkan nilai 'todo' ke ENUM + pertahankan default 'pending'
        foreach ($this->cols as $col) {
            DB::statement("
                ALTER TABLE `tracks`
                MODIFY `$col` ENUM('todo','pending','done','rejected') NOT NULL DEFAULT 'pending'
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pastikan tidak ada nilai 'todo' sebelum revert ENUM
        DB::table('tracks')->whereIn('status_invite', ['todo'])->update(['status_invite' => 'pending']);
        DB::table('tracks')->whereIn('status_respond', ['todo'])->update(['status_respond' => 'pending']);
        DB::table('tracks')->whereIn('status_docs', ['todo'])->update(['status_docs' => 'pending']);
        DB::table('tracks')->whereIn('status_draft', ['todo'])->update(['status_draft' => 'pending']);
        DB::table('tracks')->whereIn('status_schedule', ['todo'])->update(['status_schedule' => 'pending']);
        DB::table('tracks')->whereIn('status_sign', ['todo'])->update(['status_sign' => 'pending']);
        DB::table('tracks')->whereIn('status_print', ['todo'])->update(['status_print' => 'pending']);

        // Kembalikan ENUM seperti semula (tanpa 'todo')
        foreach ($this->cols as $col) {
            DB::statement("
                ALTER TABLE `tracks`
                MODIFY `$col` ENUM('pending','done','rejected') NOT NULL DEFAULT 'pending'
            ");
        }
    }
};
