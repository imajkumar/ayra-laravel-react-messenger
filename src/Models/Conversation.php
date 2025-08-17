<?php

namespace LaraChat\ChatPackage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conversation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'type',
        'avatar',
        'settings',
        'created_by',
        'is_active',
        'last_message_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'last_message_at' => 'datetime',
    ];

    protected $dates = [
        'last_message_at',
    ];

    /**
     * Get the user who created the conversation.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'created_by');
    }

    /**
     * Get the participants in the conversation.
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(config('auth.providers.users.model'), 'chat_participants', 'conversation_id', 'user_id')
            ->withPivot(['role', 'permissions', 'joined_at', 'last_read_at', 'is_muted', 'is_blocked'])
            ->withTimestamps();
    }

    /**
     * Get the messages in the conversation.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'conversation_id');
    }

    /**
     * Get the pinned messages in the conversation.
     */
    public function pinnedMessages(): HasMany
    {
        return $this->hasMany(PinnedMessage::class, 'conversation_id');
    }

    /**
     * Get the typing indicators for the conversation.
     */
    public function typingIndicators(): HasMany
    {
        return $this->hasMany(TypingIndicator::class, 'conversation_id');
    }

    /**
     * Get the latest message in the conversation.
     */
    public function latestMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'id', 'conversation_id')
            ->latest();
    }

    /**
     * Check if a user is a participant in the conversation.
     */
    public function hasParticipant($userId): bool
    {
        return $this->participants()->where('user_id', $userId)->exists();
    }

    /**
     * Check if a user has a specific role in the conversation.
     */
    public function hasRole($userId, $role): bool
    {
        return $this->participants()
            ->where('user_id', $userId)
            ->where('role', $role)
            ->exists();
    }

    /**
     * Check if a user is an admin in the conversation.
     */
    public function isAdmin($userId): bool
    {
        return $this->hasRole($userId, 'admin');
    }

    /**
     * Check if a user is a moderator in the conversation.
     */
    public function isModerator($userId): bool
    {
        return $this->hasRole($userId, 'moderator') || $this->isAdmin($userId);
    }

    /**
     * Get unread message count for a user.
     */
    public function getUnreadCount($userId): int
    {
        $participant = $this->participants()
            ->where('user_id', $userId)
            ->first();

        if (!$participant || !$participant->pivot->last_read_at) {
            return $this->messages()->count();
        }

        return $this->messages()
            ->where('created_at', '>', $participant->pivot->last_read_at)
            ->count();
    }

    /**
     * Mark messages as read for a user.
     */
    public function markAsRead($userId): void
    {
        $this->participants()
            ->updateExistingPivot($userId, [
                'last_read_at' => now(),
            ]);
    }

    /**
     * Scope for active conversations.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for conversations by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for conversations created by a user.
     */
    public function scopeByCreator($query, $userId)
    {
        return $query->where('created_by', $userId);
    }
}
