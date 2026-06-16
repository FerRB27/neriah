<?php

namespace App\Domains\SocialFund\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SocialFundBeneficiary extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'address',
        'notes',
    ];

    public function movements(): HasMany
    {
        return $this->hasMany(SocialFundMovement::class);
    }
}
