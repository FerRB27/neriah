<?php

namespace App\Domains\Sales\Models;

use App\Domains\Customers\Models\Customer;
use App\Domains\Finance\Models\FinancialProfitAllocation;
use App\Domains\Inventory\Models\InventoryMovement;
use App\Domains\People\Models\Person;
use App\Domains\Sales\Enums\SaleStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sale extends Model
{
    protected $fillable = [
        'customer_id',
        'seller_id',
        'maker_id',
        'sales_channel_id',
        'sold_at',
        'status',
        'subtotal',
        'discount_total',
        'total_amount',
        'visible_profit',
        'hidden_profit',
        'notes',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'sold_at' => 'date',
            'status' => SaleStatus::class,
            'subtotal' => 'decimal:2',
            'discount_total' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'visible_profit' => 'decimal:2',
            'hidden_profit' => 'decimal:2',
            'confirmed_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'seller_id');
    }

    public function maker(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'maker_id');
    }

    public function salesChannel(): BelongsTo
    {
        return $this->belongsTo(SalesChannel::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(SaleLine::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function profitAllocation(): HasOne
    {
        return $this->hasOne(FinancialProfitAllocation::class);
    }
}
