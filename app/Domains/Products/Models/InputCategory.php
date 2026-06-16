<?php

namespace App\Domains\Products\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InputCategory extends Model
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

    public function inputs(): HasMany
    {
        return $this->hasMany(Input::class);
    }
}
