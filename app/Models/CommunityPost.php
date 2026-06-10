<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommunityPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'community_id',
        'user_id',
        'content',
        'reply_to_id',
        'deleted_for_everyone',
        'deleted_by_users',
        'read_by_users',
    ];

    protected $casts = [
        'deleted_by_users' => 'array',
        'read_by_users' => 'array',
        'deleted_for_everyone' => 'boolean',
    ];

    /**
     * Get the community that owns the post.
     */
    public function komunitas(): BelongsTo
    {
        return $this->belongsTo(Komunitas::class, 'community_id');
    }

    /**
     * Get the user that created the post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }



    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(CommunityPost::class, 'reply_to_id');
    }
}

