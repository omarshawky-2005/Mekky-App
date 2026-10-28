<?php

namespace App\Domain\Questions\Models;

use App\Domain\Content\Models\Pdf;
use App\Domain\Videos\Models\ExplanationVideo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'pdf_id',
        'question_number',
        'reports_count',
        'last_reported_at',
    ];

    protected $casts = [
        'question_number' => 'integer',
        'reports_count' => 'integer',
        'last_reported_at' => 'datetime',
    ];

    public function pdf(): BelongsTo
    {
        return $this->belongsTo(Pdf::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(QuestionReport::class);
    }

    // تم تحديث الاسم ليكون أبسط
    public function videos(): HasMany
    {
        return $this->hasMany(ExplanationVideo::class);
    }
}