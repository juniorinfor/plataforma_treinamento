<?php

namespace App\Models;

use App\Enums\CourseDifficulty;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'created_by', 'title', 'slug', 'description',
        'short_description', 'thumbnail_path', 'cover_path', 'category_id',
        'difficulty', 'estimated_hours', 'is_published', 'is_mandatory',
        'is_platform_course', 'published_at', 'sort_order', 'xp_reward',
    ];

    protected function casts(): array
    {
        return [
            'difficulty' => CourseDifficulty::class,
            'estimated_hours' => 'decimal:1',
            'is_published' => 'boolean',
            'is_mandatory' => 'boolean',
            'is_platform_course' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class)->orderBy('sort_order');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function getTotalLessonsAttribute(): int
    {
        return $this->modules->sum(fn($m) => $m->lessons->count());
    }
}
