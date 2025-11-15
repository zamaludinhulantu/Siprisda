<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('researches', function (Blueprint $table) {
            // Tambahan data registrasi awal
            $table->string('researcher_nik', 32)->after('author');
            $table->date('start_date')->nullable()->after('year');
            $table->date('end_date')->nullable()->after('start_date');

            // Verifikasi Kesbangpol
            $table->timestamp('kesbang_verified_at')->nullable()->after('submitted_at');
            $table->foreignId('kesbang_verified_by')->nullable()->after('kesbang_verified_at')
                ->constrained('users')->nullOnDelete();

            // Penanda unggah hasil
            $table->timestamp('results_uploaded_at')->nullable()->after('approved_at');

            // Catatan: kolom pdf_path tetap non-nullable; nilai awal dapat berupa string kosong
        });
    }

    public function down(): void
    {
        Schema::table('researches', function (Blueprint $table) {
            $table->dropColumn(['researcher_nik', 'start_date', 'end_date', 'kesbang_verified_at', 'results_uploaded_at']);
            $table->dropConstrainedForeignId('kesbang_verified_by');
        });
    }
};

