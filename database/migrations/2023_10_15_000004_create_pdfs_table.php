<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pdfs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lessons')->cascadeOnDelete();
            $table->string('title', 150);
            $table->string('file_path', 255);
            $table->timestamps();
            $table->softDeletes(); // مهم جداً للاستبدال والحذف الآمن

            // Indexes
            $table->index('lesson_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pdfs');
    }
};