<?php

namespace App\Domains\SocialFund\Models;

use App\Domains\Finance\Models\FinancialProfitAllocation;
use App\Domains\SocialFund\Enums\SocialFundMovementType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialFundMovement extends Model
{
    protected $fillable = [
        'financial_profit_allocation_id',
        'social_fund_beneficiary_id',
        'type',
        'amount',
        'movement_date',
        'evidence_path',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'type' => SocialFundMovementType::class,
            'amount' => 'decimal:2',
            'movement_date' => 'date',
        ];
    }

    public function allocation(): BelongsTo
    {
        return $this->belongsTo(FinancialProfitAllocation::class, 'financial_profit_allocation_id');
    }

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(SocialFundBeneficiary::class, 'social_fund_beneficiary_id');
    }
}
