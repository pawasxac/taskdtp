<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kecamatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Get the coffee shops for the kecamatan.
     */
    public function coffeeShops(): HasMany
    {
        return $this->hasMany(CoffeeShop::class);
    }
}

