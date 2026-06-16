<?php

namespace App\Domains\Products\Models;

use App\Domains\Inventory\Models\InventoryItem;
use App\Domains\Recipes\Models\Recipe;
use App\Domains\Sales\Models\Promotion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'name',
        'unit_label',
        'units_per_variant',
        'weight_grams',
        'price',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'units_per_variant' => 'integer',
            'weight_grams' => 'decimal:2',
            'price' => 'decimal:2',
            'active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function inventoryItem(): HasOne
    {
        return $this->hasOne(InventoryItem::class);
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(Promotion::class);
    }
}
