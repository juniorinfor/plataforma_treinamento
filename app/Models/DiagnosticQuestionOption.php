<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiagnosticQuestionOption extends Model
{
    protected $fillable = [
        'diagnostic_question_id', 'content', 'value', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(DiagnosticQuestion::class, 'diagnostic_question_id');
    }
}
