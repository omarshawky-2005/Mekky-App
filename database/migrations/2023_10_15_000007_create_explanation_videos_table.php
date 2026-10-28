<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('explanation_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            
            $table->string('title', 150);
            $table->string('external_id', 50)->comment('YouTube Video ID'); // أصبح مطلوباً دائماً
            
            $table->boolean('is_published')->default(false);
            
            $table->timestamps();
            $table->softDeletes();

            $table->index(['question_id', 'is_published']);
            $table->index(['teacher_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('explanation_videos');
    }
};