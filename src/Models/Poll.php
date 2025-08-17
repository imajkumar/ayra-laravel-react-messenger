<?php

namespace LaraChat\ChatPackage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poll extends Model
{
    protected $table = 'chat_polls';

    protected $fillable = [
        'conversation_id',
        'user_id',
        'question',
        'options',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'options' => 'array',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the conversation that owns the poll
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get the user who created the poll
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Get the votes for this poll
     */
    public function votes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }

    /**
     * Check if the poll has expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Get the total vote count
     */
    public function getTotalVotes(): int
    {
        return $this->votes()->count();
    }

    /**
     * Get vote counts for each option
     */
    public function getOptionVotes(): array
    {
        $votes = [];
        foreach ($this->options as $index => $option) {
            $votes[$index] = $this->votes()->where('option_index', $index)->count();
        }
        return $votes;
    }

    /**
     * Check if a user has voted on this poll
     */
    public function hasUserVoted(int $userId): bool
    {
        return $this->votes()->where('user_id', $userId)->exists();
    }

    /**
     * Get the user's vote on this poll
     */
    public function getUserVote(int $userId): ?PollVote
    {
        return $this->votes()->where('user_id', $userId)->first();
    }
}
