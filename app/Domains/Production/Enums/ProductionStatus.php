<?php

namespace App\Domains\Production\Enums;

enum ProductionStatus: string
{
    case Draft = 'draft';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';
}
