<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tambah nilai 'kesbang_verified' ke kolom status.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE researches
            MODIFY status ENUM('draft','submitted','kesbang_verified','approved','rejected')
            NOT NULL DEFAULT 'draft'
        ");
    }

    /**
     * Kembalikan ke enum semula (tanpa kesbang_verified).
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE researches
            MODIFY status ENUM('draft','submitted','approved','rejected')
            NOT NULL DEFAULT 'draft'
        ");
    }
};
