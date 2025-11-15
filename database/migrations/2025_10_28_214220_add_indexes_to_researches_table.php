<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('researches', function (Blueprint $table) {
            $table->index('status');
            $table->index('approved_at');
            $table->index('year');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('researches', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['approved_at']);
            $table->dropIndex(['year']);
            $table->dropIndex(['created_at']);
        });
    }
};
