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

    /**
     * Get the comments for the post.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(CommunityComment::class, 'post_id');
    }

    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(CommunityPost::class, 'reply_to_id');
    }
}

