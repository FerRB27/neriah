<?php

namespace App\Domains\Finance\Models;

use App\Domains\Sales\Models\Sale;
use App\Domains\SocialFund\Models\SocialFundMovement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancialProfitAllocation extends Model
{
    protected $fillable = [
        'sale_id',
        'profit_amount',
        'social_fund_amount',
        'reinvestment_amount',
        'founder_recovery_amount',
        'reserve_amount',
        'allocated_at',
    ];

    protected function casts(): array
    {
        return [
            'profit_amount' => 'decimal:2',
            'social_fund_amount' => 'decimal:2',
            'reinvestment_amount' => 'decimal:2',
            'founder_recovery_amount' => 'decimal:2',
            'reserve_amount' => 'decimal:2',
            'allocated_at' => 'date',
        ];
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function socialFundMovements(): HasMany
    {
        return $this->hasMany(SocialFundMovement::class);
    }
}
