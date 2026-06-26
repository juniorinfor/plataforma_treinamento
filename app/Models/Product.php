<?php

namespace App\Models;

use App\Enums\ProductType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'type',
        'price_once', 'price_monthly', 'price_yearly',
        'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'type'          => ProductType::class,
            'is_active'     => 'boolean',
            'price_once'    => 'decimal:2',
            'price_monthly' => 'decimal:2',
            'price_yearly'  => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProductItem::class);
    }

    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(Plan::class, 'plan_products');
    }
}
