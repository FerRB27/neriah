<?php

namespace App\Domains\Sales\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesChannel extends Model
{
    protected $fillable = [
        'name',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
