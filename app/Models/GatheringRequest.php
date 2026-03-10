<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GatheringRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'community_id',
        'coffee_shop_id',
        'requested_by',
        'title',
        'description',
        'event_date',
        'status',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    /**
     * Get the community that requested the gathering.
     */
    public function komunitas(): BelongsTo
    {
        return $this->belongsTo(Komunitas::class, 'community_id');
    }

    /**
     * Get the coffee shop for the gathering.
     */
    public function coffeeShop(): BelongsTo
    {
        return $this->belongsTo(CoffeeShop::class);
    }

    /**
     * Get the user who requested the gathering.
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
}

