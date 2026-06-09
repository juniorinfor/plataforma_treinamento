<?php

namespace App\Models;

use App\Enums\DiagnosticReportStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiagnosticReport extends Model
{
    protected $fillable = [
        'diagnostic_assessment_id', 'ai_provider_id', 'status', 'ai_draft',
        'content', 'archetype', 'swot', 'highlights',
        'reviewed_by', 'reviewed_at', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => DiagnosticReportStatus::class,
            'swot' => 'array',
            'highlights' => 'array',
            'reviewed_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(DiagnosticAssessment::class, 'diagnostic_assessment_id');
    }

    public function aiProvider(): BelongsTo
    {
        return $this->belongsTo(AiProvider::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
