<?php

namespace App\Domains\Recipes\Models;

use App\Domains\Products\Models\ProductVariant;
use App\Domains\Production\Models\ProductionOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    protected $fillable = [
        'product_variant_id',
        'name',
        'expected_yield',
        'yield_unit',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'expected_yield' => 'decimal:4',
            'active' => 'boolean',
        ];
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    public function productionOrders(): HasMany
    {
        return $this->hasMany(ProductionOrder::class);
    }
}
