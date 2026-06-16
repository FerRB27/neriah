<?php

namespace App\Domains\Inventory\Enums;

enum InventoryMovementType: string
{
    case Purchase = 'purchase';
    case ProductionConsumption = 'production_consumption';
    case ProductionOutput = 'production_output';
    case Sale = 'sale';
    case Adjustment = 'adjustment';
    case Waste = 'waste';
    case Delivery = 'delivery';
    case Return = 'return';
}
