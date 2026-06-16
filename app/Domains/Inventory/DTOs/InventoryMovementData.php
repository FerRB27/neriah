<?php

namespace App\Domains\Inventory\DTOs;

use App\Domains\Inventory\Enums\InventoryMovementDirection;
use App\Domains\Inventory\Enums\InventoryMovementType;
use Illuminate\Support\Carbon;

readonly class InventoryMovementData
{
    public function __construct(
        public int $inventoryItemId,
        public InventoryMovementType $type,
        public InventoryMovementDirection $direction,
        public float $quantity,
        public float $unitCost = 0,
        public ?Carbon $movementDate = null,
        public ?int $purchaseId = null,
        public ?int $purchaseLineId = null,
        public ?int $productionOrderId = null,
        public ?int $saleId = null,
        public ?int $saleLineId = null,
        public ?string $notes = null,
    ) {
    }
}
