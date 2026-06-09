<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiProvider extends Model
{
    protected $fillable = [
        'name', 'driver', 'model', 'api_key', 'endpoint',
        'max_tokens', 'temperature', 'is_active', 'settings',
    ];

    protected $hidden = [
        'api_key',
    ];

    protected function casts(): array
    {
        return [
            'api_key' => 'encrypted',
            'max_tokens' => 'integer',
            'temperature' => 'decimal:2',
            'is_active' => 'boolean',
            'settings' => 'array',
        ];
    }

    public function tools(): HasMany
    {
        return $this->hasMany(DiagnosticTool::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(DiagnosticReport::class);
    }
}
