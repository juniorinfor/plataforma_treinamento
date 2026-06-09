<?php

namespace App\Models;

use App\Enums\DiagnosticQuestionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiagnosticQuestion extends Model
{
    protected $fillable = [
        'diagnostic_tool_id', 'diagnostic_dimension_id', 'type', 'content',
        'help_text', 'is_required', 'reverse_scored', 'weight', 'sort_order', 'settings',
    ];

    protected function casts(): array
    {
        return [
            'type' => DiagnosticQuestionType::class,
            'is_required' => 'boolean',
            'reverse_scored' => 'boolean',
            'weight' => 'decimal:2',
            'sort_order' => 'integer',
            'settings' => 'array',
        ];
    }

    public function tool(): BelongsTo
    {
        return $this->belongsTo(DiagnosticTool::class, 'diagnostic_tool_id');
    }

    public function dimension(): BelongsTo
    {
        return $this->belongsTo(DiagnosticDimension::class, 'diagnostic_dimension_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(DiagnosticQuestionOption::class)->orderBy('sort_order');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(DiagnosticAnswer::class);
    }
}
