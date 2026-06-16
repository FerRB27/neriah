<?php

namespace App\Domains\Commissions\Enums;

enum CommissionStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Paid = 'paid';
    case Cancelled = 'cancelled';
}
