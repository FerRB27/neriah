<?php

namespace App\Domains\Production\Models;

use App\Domains\Inventory\Models\InventoryItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionConsumption extends Model
{
    protected $fillable = [
        'production_order_id',
        'inventory_item_id',
        'planned_quantity',
        'consumed_quantity',
        'unit_cost',
        'total_cost',
    ];

    protected function casts(): array
    {
        return [
            'planned_quantity' => 'decimal:4',
            'consumed_quantity' => 'decimal:4',
            'unit_cost' => 'decimal:6',
            'total_cost' => 'decimal:2',
        ];
    }

    public function productionOrder(): BelongsTo
    {
        return $this->belongsTo(ProductionOrder::class);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }
}
