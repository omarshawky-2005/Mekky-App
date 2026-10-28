<?php

namespace App\Domain\Payments\Models;

use App\Enums\PurchaseStatus;
use App\Domain\Auth\Models\User;
use App\Domain\Videos\Models\ExplanationVideo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class VideoPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'video_id',
        'teacher_id',
        'amount',
        'currency',
        'status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => PurchaseStatus::class,
            'paid_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function video(): BelongsTo
    {
        return $this->belongsTo(ExplanationVideo::class, 'video_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'purchase_id');
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('status', PurchaseStatus::Paid);
    }
}