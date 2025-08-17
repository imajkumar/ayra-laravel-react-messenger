<?php

namespace LaraChat\ChatPackage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'parent_id',
        'content',
        'type',
        'metadata',
        'reactions',
        'is_edited',
        'edited_at',
        'is_deleted',
        'deleted_at',
        'scheduled_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'reactions' => 'array',
        'is_edited' => 'boolean',
        'is_deleted' => 'boolean',
        'scheduled_at' => 'datetime',
    ];

    protected $dates = [
        'edited_at',
        'deleted_at',
        'scheduled_at',
    ];

    /**
     * Get the conversation that owns the message.
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }

    /**
     * Get the user who sent the message.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }

    /**
     * Get the parent message (for threaded conversations).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'parent_id');
    }

    /**
     * Get the replies to this message.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Message::class, 'parent_id');
    }

    /**
     * Get the files attached to the message.
     */
    public function files(): HasMany
    {
        return $this->hasMany(ChatFile::class, 'message_id');
    }

    /**
     * Get the reactions on the message.
     */
    public function messageReactions(): HasMany
    {
        return $this->hasMany(Reaction::class, 'message_id');
    }

    /**
     * Get the read receipts for the message.
     */
    public function readReceipts(): HasMany
    {
        return $this->hasMany(ReadReceipt::class, 'message_id');
    }

    /**
     * Get the pinned message record.
     */
    public function pinnedMessage(): HasOne
    {
        return $this->hasOne(PinnedMessage::class, 'message_id');
    }

    /**
     * Check if the message is a reply.
     */
    public function isReply(): bool
    {
        return !is_null($this->parent_id);
    }

    /**
     * Check if the message has replies.
     */
    public function hasReplies(): bool
    {
        return $this->replies()->exists();
    }

    /**
     * Check if the message has files.
     */
    public function hasFiles(): bool
    {
        return $this->files()->exists();
    }

    /**
     * Check if the message has reactions.
     */
    public function hasReactions(): bool
    {
        return !empty($this->reactions);
    }

    /**
     * Check if the message is scheduled.
     */
    public function isScheduled(): bool
    {
        return !is_null($this->scheduled_at) && $this->scheduled_at->isFuture();
    }

    /**
     * Check if the message is overdue (scheduled but not sent).
     */
    public function isOverdue(): bool
    {
        return !is_null($this->scheduled_at) && $this->scheduled_at->isPast();
    }

    /**
     * Get the reaction count for a specific emoji.
     */
    public function getReactionCount($emoji): int
    {
        if (!$this->reactions) {
            return 0;
        }

        return collect($this->reactions)->where('emoji', $emoji)->count();
    }

    /**
     * Check if a user has reacted with a specific emoji.
     */
    public function hasUserReaction($userId, $emoji): bool
    {
        if (!$this->reactions) {
            return false;
        }

        return collect($this->reactions)->contains(function ($reaction) use ($userId, $emoji) {
            return $reaction['user_id'] == $userId && $reaction['emoji'] == $emoji;
        });
    }

    /**
     * Add a reaction to the message.
     */
    public function addReaction($userId, $emoji): void
    {
        $reactions = $this->reactions ?? [];
        
        // Check if user already reacted with this emoji
        $existingIndex = collect($reactions)->search(function ($reaction) use ($userId, $emoji) {
            return $reaction['user_id'] == $userId && $reaction['emoji'] == $emoji;
        });

        if ($existingIndex !== false) {
            return; // Already reacted
        }

        $reactions[] = [
            'user_id' => $userId,
            'emoji' => $emoji,
            'created_at' => now()->toISOString(),
        ];

        $this->update(['reactions' => $reactions]);
    }

    /**
     * Remove a reaction from the message.
     */
    public function removeReaction($userId, $emoji): void
    {
        if (!$this->reactions) {
            return;
        }

        $reactions = collect($this->reactions)->filter(function ($reaction) use ($userId, $emoji) {
            return !($reaction['user_id'] == $userId && $reaction['emoji'] == $emoji);
        })->values()->toArray();

        $this->update(['reactions' => $reactions]);
    }

    /**
     * Scope for messages by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for messages by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for scheduled messages.
     */
    public function scopeScheduled($query)
    {
        return $query->whereNotNull('scheduled_at');
    }

    /**
     * Scope for overdue messages.
     */
    public function scopeOverdue($query)
    {
        return $query->where('scheduled_at', '<', now());
    }
}
