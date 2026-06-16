<?php

namespace App\Domains\SocialFund\Enums;

enum SocialFundMovementType: string
{
    case Allocation = 'allocation';
    case Donation = 'donation';
    case Adjustment = 'adjustment';
}
