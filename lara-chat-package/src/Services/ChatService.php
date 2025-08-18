<?php

namespace LaraChat\ChatPackage\Services;

use LaraChat\ChatPackage\Models\Conversation;
use LaraChat\ChatPackage\Models\Message;
use LaraChat\ChatPackage\Models\File;
use LaraChat\ChatPackage\Models\Poll;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;

class ChatService
{
    /**
     * Get user conversations
     */
    public function getUserConversations(int $userId): Collection
    {
        return Conversation::whereHas('participants', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with(['participants.user', 'lastMessage'])->get();
    }

    /**
     * Get unread message counts for user
     */
    public function getUnreadCounts(int $userId): Collection
    {
        return Conversation::whereHas('participants', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->withCount(['messages as unread_count' => function ($query) use ($userId) {
            $query->whereDoesntHave('readReceipts', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }])->get();
    }

    /**
     * Create a new conversation
     */
    public function createConversation(array $data): Conversation
    {
        $conversation = Conversation::create([
            'name' => $data['name'] ?? null,
            'type' => $data['type'] ?? 'private',
            'creator_id' => $data['creator_id'],
            'is_group' => $data['is_group'] ?? false,
        ]);

        // Add participants
        if (isset($data['participant_ids'])) {
            foreach ($data['participant_ids'] as $participantId) {
                $conversation->participants()->create([
                    'user_id' => $participantId,
                    'role' => $participantId === $data['creator_id'] ? 'admin' : 'member',
                ]);
            }
        }

        return $conversation->load('participants.user');
    }

    /**
     * Create a new message
     */
    public function createMessage(array $data): Message
    {
        $message = Message::create([
            'conversation_id' => $data['conversation_id'],
            'user_id' => $data['user_id'],
            'content' => $data['content'],
            'type' => $data['type'] ?? 'text',
            'parent_id' => $data['parent_id'] ?? null,
        ]);

        return $message->load(['user', 'files']);
    }

    /**
     * Update a message
     */
    public function updateMessage(int $messageId, array $data): Message
    {
        $message = Message::findOrFail($messageId);
        $message->update($data);
        
        return $message->fresh();
    }

    /**
     * Delete a message
     */
    public function deleteMessage(int $messageId): bool
    {
        $message = Message::findOrFail($messageId);
        return $message->delete();
    }

    /**
     * Upload a file
     */
    public function uploadFile(UploadedFile $file, int $conversationId): File
    {
        $path = $file->store('chat-files', 'public');
        
        return File::create([
            'conversation_id' => $conversationId,
            'user_id' => auth()->id(),
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);
    }

    /**
     * Delete a file
     */
    public function deleteFile(int $fileId): bool
    {
        $file = File::findOrFail($fileId);
        Storage::disk('public')->delete($file->path);
        return $file->delete();
    }

    /**
     * Add reaction to message
     */
    public function addReaction(int $messageId, int $userId, string $emoji): Message
    {
        $message = Message::findOrFail($messageId);
        $message->addReaction($userId, $emoji);
        return $message->fresh();
    }

    /**
     * Remove reaction from message
     */
    public function removeReaction(int $messageId, int $userId, string $emoji): bool
    {
        $message = Message::findOrFail($messageId);
        return $message->removeReaction($userId, $emoji);
    }

    /**
     * Get replies to a message
     */
    public function getReplies(int $messageId): Collection
    {
        return Message::where('parent_id', $messageId)
            ->with(['user', 'files'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Create a reply to a message
     */
    public function createReply(array $data): Message
    {
        return $this->createMessage($data);
    }

    /**
     * Pin a message
     */
    public function pinMessage(int $messageId): bool
    {
        $message = Message::findOrFail($messageId);
        return $message->pin();
    }

    /**
     * Unpin a message
     */
    public function unpinMessage(int $messageId): bool
    {
        $message = Message::findOrFail($messageId);
        return $message->unpin();
    }

    /**
     * Start typing indicator
     */
    public function startTyping(int $conversationId, int $userId): bool
    {
        // Implementation for typing indicator
        return true;
    }

    /**
     * Stop typing indicator
     */
    public function stopTyping(int $conversationId, int $userId): bool
    {
        // Implementation for typing indicator
        return true;
    }

    /**
     * Mark conversation as read for user
     */
    public function markAsRead(int $conversationId, int $userId): bool
    {
        $conversation = Conversation::findOrFail($conversationId);
        return $conversation->markAsRead($userId);
    }

    /**
     * Search messages
     */
    public function search(string $query, int $userId): Collection
    {
        return Message::whereHas('conversation.participants', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->where('content', 'like', "%{$query}%")
        ->with(['conversation', 'user'])
        ->get();
    }

    /**
     * Create a poll
     */
    public function createPoll(int $conversationId, int $userId, array $data): Poll
    {
        return Poll::create([
            'conversation_id' => $conversationId,
            'user_id' => $userId,
            'question' => $data['question'],
            'options' => $data['options'],
            'expires_at' => $data['expires_at'] ?? null,
        ]);
    }

    /**
     * Vote on a poll
     */
    public function votePoll(int $pollId, int $userId, int $optionIndex): bool
    {
        $poll = Poll::findOrFail($pollId);
        // Implementation for voting
        return true;
    }

    /**
     * Schedule a message
     */
    public function scheduleMessage(array $data): Message
    {
        return Message::create([
            'conversation_id' => $data['conversation_id'],
            'user_id' => $data['user_id'],
            'content' => $data['content'],
            'type' => $data['type'] ?? 'text',
            'scheduled_at' => $data['scheduled_at'],
            'is_scheduled' => true,
        ]);
    }

    /**
     * Cancel a scheduled message
     */
    public function cancelScheduledMessage(int $messageId): bool
    {
        $message = Message::findOrFail($messageId);
        return $message->delete();
    }

    /**
     * Get conversation statistics
     */
    public function getConversationStats(): array
    {
        return [
            'total_conversations' => Conversation::count(),
            'total_messages' => Message::count(),
            'total_users' => \App\Models\User::count(),
        ];
    }

    /**
     * Get user activity
     */
    public function getUserActivity(int $userId): array
    {
        return [
            'conversations_count' => $this->getUserConversations($userId)->count(),
            'messages_count' => Message::where('user_id', $userId)->count(),
            'last_activity' => Message::where('user_id', $userId)->latest()->first()?->created_at,
        ];
    }

    /**
     * Get file usage statistics
     */
    public function getFileUsageStats(): array
    {
        return [
            'total_files' => File::count(),
            'total_size' => File::sum('size'),
            'files_by_type' => File::selectRaw('mime_type, COUNT(*) as count')
                ->groupBy('mime_type')
                ->get(),
        ];
    }
}
