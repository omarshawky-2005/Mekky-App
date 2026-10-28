<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pdf_id')->constrained('pdfs')->cascadeOnDelete();
            $table->integer('question_number');
            $table->integer('reports_count')->default(0);
            $table->timestamp('last_reported_at')->nullable();
            $table->timestamps();

            // Constraints & Indexes (مهم جداً للأداء والتجميع)
            $table->unique(['pdf_id', 'question_number']); // يمنع تكرار نفس السؤال في نفس الـ PDF
            $table->index(['pdf_id', 'reports_count']); // لترتيب داشبورد المدرس بسرعة
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};