<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiagnosticToolComponent extends Model
{
    protected $fillable = [
        'parent_tool_id', 'child_tool_id', 'weight', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(DiagnosticTool::class, 'parent_tool_id');
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(DiagnosticTool::class, 'child_tool_id');
    }
}
