<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pdf_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pdf_id')->constrained('pdfs')->cascadeOnDelete();
            $table->timestamp('last_opened_at')->useCurrent();
            $table->timestamps();

            // Constraint
            $table->unique(['student_id', 'pdf_id']); // upsert: سجل واحد فقط لكل طالب و PDF
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pdf_views');
    }
};