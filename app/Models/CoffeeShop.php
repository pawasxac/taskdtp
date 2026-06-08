<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoffeeShop extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'nama',
        'daerah',
        'kecamatan',
        'alamat',
        'jam_buka',
        'jam_tutup',
        'harga_min',
        'harga_max',
        'rating',
        'deskripsi',
        'kecamatan_id',
        'latitude',
        'longitude',
        'photo_url',
        'is_verified',
        'is_active',
    ];

    /**
     * Get the kecamatan that owns the coffee shop.
     */
    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class);
    }

    /**
     * Get the reviews for the coffee shop.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(CoffeeShopReview::class);
    }

    /**
     * Get the gathering requests for the coffee shop.
     */
    public function gatheringRequests(): HasMany
    {
        return $this->hasMany(GatheringRequest::class);
    }
}

