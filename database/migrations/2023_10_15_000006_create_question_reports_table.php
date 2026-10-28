<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->text('question_text')->nullable(); // نص السؤال اختياري
            $table->timestamps();

            // Constraints & Indexes
            $table->unique(['question_id', 'student_id']); // يمنع الطالب من الإبلاغ عن نفس السؤال مرتين
            $table->index(['student_id', 'created_at']); // لصفحة "أسئلتي"
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_reports');
    }
};