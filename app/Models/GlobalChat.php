<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GlobalChat extends Model
{
    protected $fillable = [
        'user_id',
        'message',
        'reply_to_id',
    ];

    /**
     * Get the user that owns the chat message.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(GlobalChat::class, 'reply_to_id');
    }
}
