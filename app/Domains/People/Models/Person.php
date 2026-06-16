<?php

namespace App\Domains\People\Models;

use App\Domains\Assets\Models\AssetAssignment;
use App\Domains\Commissions\Models\CommissionEntry;
use App\Domains\Payments\Models\Payment;
use App\Domains\Production\Models\ProductionOrder;
use App\Domains\Sales\Models\Sale;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Person extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function roleAssignments(): HasMany
    {
        return $this->hasMany(PersonRoleAssignment::class);
    }

    public function productionOrders(): HasMany
    {
        return $this->hasMany(ProductionOrder::class, 'maker_id');
    }

    public function salesAsSeller(): HasMany
    {
        return $this->hasMany(Sale::class, 'seller_id');
    }

    public function salesAsMaker(): HasMany
    {
        return $this->hasMany(Sale::class, 'maker_id');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(CommissionEntry::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function assetAssignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }
}
