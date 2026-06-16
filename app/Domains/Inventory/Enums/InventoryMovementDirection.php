<?php

namespace App\Domains\Inventory\Enums;

enum InventoryMovementDirection: string
{
    case In = 'in';
    case Out = 'out';
}
