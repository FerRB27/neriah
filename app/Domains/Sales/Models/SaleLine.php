<?php

namespace App\Domains\Sales\Models;

use App\Domains\Inventory\Models\InventoryItem;
use App\Domains\Inventory\Models\InventoryMovement;
use App\Domains\Products\Models\ProductVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SaleLine extends Model
{
    protected $fillable = [
        'sale_id',
        'product_variant_id',
        'inventory_item_id',
        'promotion_id',
        'quantity',
        'unit_price',
        'standard_unit_cost',
        'real_unit_cost',
        'line_total',
        'visible_profit',
        'hidden_profit',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:4',
            'unit_price' => 'decimal:2',
            'standard_unit_cost' => 'decimal:6',
            'real_unit_cost' => 'decimal:6',
            'line_total' => 'decimal:2',
            'visible_profit' => 'decimal:2',
            'hidden_profit' => 'decimal:2',
        ];
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }
}
