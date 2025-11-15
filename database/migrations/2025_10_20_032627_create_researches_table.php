<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('researches', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('author');
        $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
        $table->foreignId('field_id')->constrained()->cascadeOnDelete();
        $table->year('year');
        $table->text('abstract')->nullable();
        $table->string('keywords')->nullable();
        $table->string('pdf_path'); // path file PDF
        $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
        $table->foreignId('submitted_by')->constrained('users');
        $table->timestamp('submitted_at')->nullable();
        $table->timestamp('approved_at')->nullable();
        $table->timestamp('rejected_at')->nullable();
        $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
        $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('researches');
    }
};
