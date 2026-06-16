<?php

namespace App\Domains\Purchases\Actions;

use App\Domains\Inventory\DTOs\InventoryMovementData;
use App\Domains\Inventory\Enums\InventoryMovementDirection;
use App\Domains\Inventory\Enums\InventoryMovementType;
use App\Domains\Inventory\Services\KardexService;
use App\Domains\Purchases\Models\Purchase;
use Illuminate\Support\Facades\DB;

class ConfirmPurchaseAction
{
    public function __construct(
        private readonly KardexService $kardex,
    ) {
    }

    public function execute(Purchase $purchase): Purchase
    {
        return DB::transaction(function () use ($purchase): Purchase {
            $purchase->loadMissing('lines');

            foreach ($purchase->lines as $line) {
                $this->kardex->record(new InventoryMovementData(
                    inventoryItemId: $line->inventory_item_id,
                    type: InventoryMovementType::Purchase,
                    direction: InventoryMovementDirection::In,
                    quantity: (float) $line->quantity,
                    unitCost: (float) $line->unit_cost,
                    movementDate: $purchase->purchased_at,
                    purchaseId: $purchase->id,
                    purchaseLineId: $line->id,
                    notes: 'Compra confirmada',
                ));
            }

            $purchase->forceFill([
                'status' => 'confirmed',
                'total_amount' => $purchase->lines->sum('total_cost'),
                'confirmed_at' => now(),
            ])->save();

            return $purchase;
        });
    }
}
