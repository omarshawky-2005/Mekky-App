<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('video_id')->constrained('explanation_videos')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->char('currency', 3)->default('EGP');
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            // Generated Column for unique constraint on paid purchases
            // paid_purchase_key = student_id when status = 'paid', else NULL
            $table->unsignedBigInteger('paid_purchase_key')->nullable()->storedAs("CASE WHEN status = 'paid' THEN student_id ELSE NULL END");

            // Indexes & Constraints as per plan
            $table->unique(['video_id', 'paid_purchase_key']);
            $table->index(['student_id', 'video_id', 'status']);
            $table->index(['teacher_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_purchases');
    }
};