<?php

namespace App\Domain\Content\Models;

use App\Domain\Auth\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PdfView extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'pdf_id',
        'last_opened_at',
    ];

    protected $casts = [
        'last_opened_at' => 'datetime',
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function pdf(): BelongsTo
    {
        return $this->belongsTo(Pdf::class);
    }
}