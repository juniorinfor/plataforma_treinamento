<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiagnosticDimension extends Model
{
    protected $fillable = [
        'diagnostic_tool_id', 'code', 'name', 'slug', 'description',
        'icon', 'color', 'weight', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    public function tool(): BelongsTo
    {
        return $this->belongsTo(DiagnosticTool::class, 'diagnostic_tool_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(DiagnosticQuestion::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(DiagnosticResult::class);
    }
}
