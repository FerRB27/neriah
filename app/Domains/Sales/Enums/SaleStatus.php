<?php

namespace App\Domains\Sales\Enums;

enum SaleStatus: string
{
    case Draft = 'draft';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';
}
