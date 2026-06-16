<?php

namespace App\Domains\Production\Models;

use App\Domains\Inventory\Models\InventoryMovement;
use App\Domains\People\Models\Person;
use App\Domains\Products\Models\ProductVariant;
use App\Domains\Production\Enums\ProductionStatus;
use App\Domains\Recipes\Models\Recipe;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionOrder extends Model
{
    protected $fillable = [
        'maker_id',
        'recipe_id',
        'product_variant_id',
        'produced_at',
        'status',
        'planned_quantity',
        'produced_quantity',
        'real_cost_total',
        'notes',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'produced_at' => 'date',
            'status' => ProductionStatus::class,
            'planned_quantity' => 'decimal:4',
            'produced_quantity' => 'decimal:4',
            'real_cost_total' => 'decimal:2',
            'confirmed_at' => 'datetime',
        ];
    }

    public function maker(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'maker_id');
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function consumptions(): HasMany
    {
        return $this->hasMany(ProductionConsumption::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }
}
