<?php

namespace App\Domains\Products\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'product_category_id',
        'name',
        'standard_cost',
        'base_price',
        'commission_amount',
        'maker_payment_amount',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'standard_cost' => 'decimal:4',
            'base_price' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'maker_payment_amount' => 'decimal:2',
            'active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }
}
