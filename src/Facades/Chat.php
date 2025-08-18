<?php

namespace LaraChat\ChatPackage\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \LaraChat\ChatPackage\Services\ChatService getService()
 * @method static \Illuminate\Database\Eloquent\Collection getUserConversations(int $userId)
 * @method static \Illuminate\Database\Eloquent\Collection getUnreadCounts(int $userId)
 * @method static \LaraChat\ChatPackage\Models\Conversation createConversation(array $data)
 * @method static \LaraChat\ChatPackage\Models\Message createMessage(array $data)
 * @method static \LaraChat\ChatPackage\Models\Message updateMessage(int $messageId, array $data)
 * @method static bool deleteMessage(int $messageId)
 * @method static \LaraChat\ChatPackage\Models\File uploadFile(\Illuminate\Http\UploadedFile $file, int $conversationId)
 * @method static bool deleteFile(int $fileId)
 * @method static \LaraChat\ChatPackage\Models\Message addReaction(int $messageId, int $userId, string $emoji)
 * @method static bool removeReaction(int $messageId, int $userId, string $emoji)
 * @method static \Illuminate\Database\Eloquent\Collection getReplies(int $messageId)
 * @method static \LaraChat\ChatPackage\Models\Message createReply(array $data)
 * @method static bool pinMessage(int $messageId)
 * @method static bool unpinMessage(int $messageId)
 * @method static bool startTyping(int $conversationId, int $userId)
 * @method static bool stopTyping(int $conversationId, int $userId)
 * @method static bool markAsRead(int $conversationId, int $userId)
 * @method static \Illuminate\Database\Eloquent\Collection search(string $query, int $userId)
 * @method static \LaraChat\ChatPackage\Models\Poll createPoll(int $conversationId, int $userId, array $data)
 * @method static bool votePoll(int $pollId, int $userId, int $optionIndex)
 * @method static \LaraChat\ChatPackage\Models\Message scheduleMessage(array $data)
 * @method static bool cancelScheduledMessage(int $messageId)
 * @method static array getConversationStats()
 * @method static array getUserActivity(int $userId)
 * @method static array getFileUsageStats()
 *
 * @see \LaraChat\ChatPackage\Services\ChatService
 */
class Chat extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'chat';
    }
}
