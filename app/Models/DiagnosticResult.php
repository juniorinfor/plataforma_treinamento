<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiagnosticResult extends Model
{
    protected $fillable = [
        'diagnostic_assessment_id', 'component_tool_id', 'diagnostic_dimension_id',
        'raw_score', 'max_score', 'normalized_score', 'label',
    ];

    protected function casts(): array
    {
        return [
            'raw_score' => 'decimal:2',
            'max_score' => 'decimal:2',
            'normalized_score' => 'decimal:2',
        ];
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(DiagnosticAssessment::class, 'diagnostic_assessment_id');
    }

    public function componentTool(): BelongsTo
    {
        return $this->belongsTo(DiagnosticTool::class, 'component_tool_id');
    }

    public function dimension(): BelongsTo
    {
        return $this->belongsTo(DiagnosticDimension::class, 'diagnostic_dimension_id');
    }
}
