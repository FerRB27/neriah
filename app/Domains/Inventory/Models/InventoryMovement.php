<?php

namespace App\Domains\Inventory\Models;

use App\Domains\Inventory\Enums\InventoryMovementDirection;
use App\Domains\Inventory\Enums\InventoryMovementType;
use App\Domains\Production\Models\ProductionOrder;
use App\Domains\Purchases\Models\Purchase;
use App\Domains\Purchases\Models\PurchaseLine;
use App\Domains\Sales\Models\Sale;
use App\Domains\Sales\Models\SaleLine;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    protected $fillable = [
        'inventory_item_id',
        'purchase_id',
        'purchase_line_id',
        'production_order_id',
        'sale_id',
        'sale_line_id',
        'type',
        'direction',
        'quantity',
        'unit_cost',
        'total_cost',
        'running_quantity',
        'running_average_cost',
        'movement_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'type' => InventoryMovementType::class,
            'direction' => InventoryMovementDirection::class,
            'quantity' => 'decimal:4',
            'unit_cost' => 'decimal:6',
            'total_cost' => 'decimal:2',
            'running_quantity' => 'decimal:4',
            'running_average_cost' => 'decimal:6',
            'movement_date' => 'date',
        ];
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function purchaseLine(): BelongsTo
    {
        return $this->belongsTo(PurchaseLine::class);
    }

    public function productionOrder(): BelongsTo
    {
        return $this->belongsTo(ProductionOrder::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function saleLine(): BelongsTo
    {
        return $this->belongsTo(SaleLine::class);
    }
}
