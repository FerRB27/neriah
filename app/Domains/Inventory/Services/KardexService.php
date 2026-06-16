<?php

namespace App\Domains\Inventory\Services;

use App\Domains\Inventory\DTOs\InventoryMovementData;
use App\Domains\Inventory\Enums\InventoryMovementDirection;
use App\Domains\Inventory\Models\InventoryItem;
use App\Domains\Inventory\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;

class KardexService
{
    public function record(InventoryMovementData $data): InventoryMovement
    {
        return DB::transaction(function () use ($data): InventoryMovement {
            /** @var InventoryItem $item */
            $item = InventoryItem::query()
                ->lockForUpdate()
                ->findOrFail($data->inventoryItemId);

            $quantity = round($data->quantity, 4);
            $unitCost = round($data->unitCost, 6);
            $totalCost = round($quantity * $unitCost, 2);

            $previousQuantity = (float) $item->current_stock;
            $previousAverageCost = (float) $item->average_cost;

            if ($data->direction === InventoryMovementDirection::In) {
                $runningQuantity = $previousQuantity + $quantity;
                $runningAverageCost = $runningQuantity > 0
                    ? (($previousQuantity * $previousAverageCost) + $totalCost) / $runningQuantity
                    : $unitCost;
            } else {
                $runningQuantity = $previousQuantity - $quantity;
                $runningAverageCost = $previousAverageCost;
            }

            $item->forceFill([
                'current_stock' => round($runningQuantity, 4),
                'average_cost' => round($runningAverageCost, 6),
            ])->save();

            return InventoryMovement::query()->create([
                'inventory_item_id' => $item->id,
                'purchase_id' => $data->purchaseId,
                'purchase_line_id' => $data->purchaseLineId,
                'production_order_id' => $data->productionOrderId,
                'sale_id' => $data->saleId,
                'sale_line_id' => $data->saleLineId,
                'type' => $data->type,
                'direction' => $data->direction,
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
                'running_quantity' => round($runningQuantity, 4),
                'running_average_cost' => round($runningAverageCost, 6),
                'movement_date' => $data->movementDate ?? now(),
                'notes' => $data->notes,
            ]);
        });
    }
}
