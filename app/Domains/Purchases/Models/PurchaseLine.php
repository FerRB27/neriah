<?php

namespace App\Domains\Purchases\Models;

use App\Domains\Inventory\Models\InventoryItem;
use App\Domains\Inventory\Models\InventoryMovement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseLine extends Model
{
    protected $fillable = [
        'purchase_id',
        'inventory_item_id',
        'quantity',
        'unit_cost',
        'total_cost',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:4',
            'unit_cost' => 'decimal:6',
            'total_cost' => 'decimal:2',
        ];
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }
}
