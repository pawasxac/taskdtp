<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunityMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'community_id',
        'user_id',
        'role',
        'joined_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    /**
     * Get the community (komunitas) that this member belongs to.
     */
    public function komunitas(): BelongsTo
    {
        return $this->belongsTo(Komunitas::class, 'community_id');
    }

    /**
     * Get the user that is a member.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

