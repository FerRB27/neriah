<?php

namespace App\Domains\Finance\Enums;

enum FounderCapitalMovementType: string
{
    case Contribution = 'contribution';
    case Reimbursement = 'reimbursement';
    case Adjustment = 'adjustment';
}
