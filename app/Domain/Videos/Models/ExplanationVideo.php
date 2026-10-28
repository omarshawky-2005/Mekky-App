<?php

namespace App\Domain\Videos\Models;

use App\Domain\Auth\Models\User;
use App\Domain\Questions\Models\Question;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExplanationVideo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'question_id',
        'teacher_id',
        'title',
        'external_id',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // Helper method لتوليد رابط التضمين
    public function getEmbedUrlAttribute(): string
    {
        return "https://www.youtube.com/embed/{$this->external_id}";
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}