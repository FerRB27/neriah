<?php

namespace App\Domains\Inventory\Models;

use App\Domains\Products\Models\Input;
use App\Domains\Products\Models\ProductVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    protected $fillable = [
        'input_id',
        'product_variant_id',
        'sku',
        'name',
        'item_type',
        'unit',
        'minimum_stock',
        'current_stock',
        'average_cost',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'minimum_stock' => 'decimal:4',
            'current_stock' => 'decimal:4',
            'average_cost' => 'decimal:6',
            'active' => 'boolean',
        ];
    }

    public function input(): BelongsTo
    {
        return $this->belongsTo(Input::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }
}
