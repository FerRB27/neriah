<?php

namespace App\Domains\Assets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessAsset extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category',
        'acquired_at',
        'cost',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'acquired_at' => 'date',
            'cost' => 'decimal:2',
        ];
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }
}
