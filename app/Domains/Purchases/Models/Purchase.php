<?php

namespace App\Domains\Purchases\Models;

use App\Domains\Inventory\Models\InventoryMovement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    protected $fillable = [
        'supplier_id',
        'purchased_at',
        'status',
        'total_amount',
        'notes',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'purchased_at' => 'date',
            'total_amount' => 'decimal:2',
            'confirmed_at' => 'datetime',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(PurchaseLine::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }
}
