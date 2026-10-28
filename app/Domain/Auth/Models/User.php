<?php

namespace App\Domain\Auth\Models;

use App\Enums\UserRole;
use App\Domain\Content\Models\Course;
use App\Domain\Content\Models\PdfView;
use App\Domain\Payments\Models\VideoPurchase;
use App\Domain\Questions\Models\QuestionReport;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'subject',
        'telegram_link',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    // Relationships
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    public function pdfViews(): HasMany
    {
        return $this->hasMany(PdfView::class, 'student_id');
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(VideoPurchase::class, 'student_id');
    }

    public function questionReports(): HasMany
    {
        return $this->hasMany(QuestionReport::class, 'student_id');
    }

    // Scopes
    public function scopeTeachers($query)
    {
        return $query->where('role', UserRole::Teacher);
    }

    public function scopeStudents($query)
    {
        return $query->where('role', UserRole::Student);
    }
}