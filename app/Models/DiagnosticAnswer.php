<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiagnosticAnswer extends Model
{
    protected $fillable = [
        'diagnostic_assessment_id', 'diagnostic_question_id',
        'diagnostic_question_option_id', 'numeric_value', 'text_value',
    ];

    protected function casts(): array
    {
        return [
            'numeric_value' => 'decimal:2',
        ];
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(DiagnosticAssessment::class, 'diagnostic_assessment_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(DiagnosticQuestion::class, 'diagnostic_question_id');
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(DiagnosticQuestionOption::class, 'diagnostic_question_option_id');
    }
}
