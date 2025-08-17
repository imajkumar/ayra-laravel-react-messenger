<?php

namespace LaraChat\ChatPackage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PollVote extends Model
{
    protected $table = 'chat_poll_votes';

    protected $fillable = [
        'poll_id',
        'user_id',
        'option_index',
    ];

    /**
     * Get the poll that this vote belongs to
     */
    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    /**
     * Get the user who made this vote
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
