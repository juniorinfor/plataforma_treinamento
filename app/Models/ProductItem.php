<?php

namespace App\Models;

use App\Enums\ProductItemType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductItem extends Model
{
    protected $fillable = [
        'product_id', 'type', 'course_id', 'diagnostic_tool_id',
    ];

    protected function casts(): array
    {
        return [
            'type' => ProductItemType::class,
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function tool(): BelongsTo
    {
        return $this->belongsTo(DiagnosticTool::class, 'diagnostic_tool_id');
    }

    public function label(): string
    {
        return match ($this->type) {
            ProductItemType::AllCourses     => 'Todos os cursos',
            ProductItemType::AllDiagnostics => 'Todos os diagnósticos',
            ProductItemType::Course         => $this->course?->title ?? 'Curso removido',
            ProductItemType::Diagnostic     => $this->tool?->name ?? 'Diagnóstico removido',
        };
    }
}
