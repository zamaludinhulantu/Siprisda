<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('researches', function (Blueprint $table) {
            if (!Schema::hasColumn('researches', 'kesbang_letter_path')) {
                $table->string('kesbang_letter_path')
                    ->nullable()
                    ->after('pdf_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('researches', function (Blueprint $table) {
            if (Schema::hasColumn('researches', 'kesbang_letter_path')) {
                $table->dropColumn('kesbang_letter_path');
            }
        });
    }
};
