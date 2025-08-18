<?php


namespace LaraChat\ChatPackage\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;
use LaraChat\ChatPackage\Models\Conversation;
use LaraChat\ChatPackage\Models\Message;
use LaraChat\ChatPackage\Models\ChatFile;
use LaraChat\ChatPackage\Services\ChatService;
use LaraChat\ChatPackage\Events\MessageSent;
use LaraChat\ChatPackage\Events\TypingStarted;
use LaraChat\ChatPackage\Events\TypingStopped;
use LaraChat\ChatPackage\Http\Requests\StoreMessageRequest;
use LaraChat\ChatPackage\Http\Requests\UpdateMessageRequest;


class ChatController extends Controller
{
    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Display the main chat interface.
     */
    public function index(): Response
    {
        $conversations = $this->chatService->getUserConversations(auth()->id());
        $unreadCounts = $this->chatService->getUnreadCounts(auth()->id());

        return Inertia::render('Chat/Index', [
            'conversations' => $conversations,
            'unreadCounts' => $unreadCounts,
            'user' => auth()->user(),
        ]);
    }

    /**
     * Display user conversations.
     */
    public function conversations(): Response
    {
        $conversations = $this->chatService->getUserConversations(auth()->id());
        
        return Inertia::render('Chat/Conversations', [
            'conversations' => $conversations,
        ]);
    }

    /**
     * Display a specific conversation.
     */
    public function showConversation(Conversation $conversation): Response
    {
        // Check if user is participant
        if (!$conversation->hasParticipant(auth()->id())) {
            abort(403, 'You are not a participant in this conversation.');
        }

        $messages = $this->chatService->getConversationMessages($conversation->id);
        $participants = $conversation->participants;
        $pinnedMessages = $conversation->pinnedMessages()->with('message')->get();

        // Mark messages as read
        $conversation->markAsRead(auth()->id());

        return Inertia::render('Chat/Conversation', [
            'conversation' => $conversation,
            'messages' => $messages,
            'participants' => $participants,
            'pinnedMessages' => $pinnedMessages,
        ]);
    }

    /**
     * Store a new message.
     */
    public function storeMessage(StoreMessageRequest $request, Conversation $conversation): JsonResponse
    {
        // Check if user is participant
        if (!$conversation->hasParticipant(auth()->id())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message = $this->chatService->createMessage([
            'conversation_id' => $conversation->id,
            'user_id' => auth()->id(),
            'content' => $request->content,
            'type' => $request->type ?? 'text',
            'parent_id' => $request->parent_id,
            'metadata' => $request->metadata,
        ]);

        // Update conversation last message timestamp
        $conversation->update(['last_message_at' => now()]);

        // Broadcast message event
        event(new MessageSent($message));

        return response()->json([
            'message' => $message->load(['user', 'files']),
            'success' => true,
        ]);
    }

    /**
     * Update a message.
     */
    public function updateMessage(UpdateMessageRequest $request, Message $message): JsonResponse
    {
        // Check if user owns the message
        if ($message->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->update([
            'content' => $request->content,
            'is_edited' => true,
            'edited_at' => now(),
        ]);

        return response()->json([
            'message' => $message->fresh(),
            'success' => true,
        ]);
    }

    /**
     * Delete a message.
     */
    public function deleteMessage(Message $message): JsonResponse
    {
        // Check if user owns the message or is admin
        if ($message->user_id !== auth()->id() && !$message->conversation->isAdmin(auth()->id())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->update([
            'is_deleted' => true,
            'deleted_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Upload a file.
     */
    public function uploadFile(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:' . config('chat.uploads.max_size'),
            'conversation_id' => 'required|exists:chat_conversations,id',
        ]);

        $file = $this->chatService->uploadFile($request->file('file'), $request->conversation_id);

        return response()->json([
            'file' => $file,
            'success' => true,
        ]);
    }

    /**
     * Delete a file.
     */
    public function deleteFile(ChatFile $file): JsonResponse
    {
        // Check if user owns the file or is admin
        if ($file->user_id !== auth()->id() && !$file->message->conversation->isAdmin(auth()->id())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->chatService->deleteFile($file);

        return response()->json(['success' => true]);
    }

    /**
     * Add a reaction to a message.
     */
    public function addReaction(Request $request, Message $message): JsonResponse
    {
        $request->validate([
            'emoji' => 'required|string|max:10',
        ]);

        $message->addReaction(auth()->id(), $request->emoji);

        return response()->json([
            'message' => $message->fresh(),
            'success' => true,
        ]);
    }

    /**
     * Remove a reaction from a message.
     */
    public function removeReaction(Message $message, string $emoji): JsonResponse
    {
        $message->removeReaction(auth()->id(), $emoji);

        return response()->json([
            'message' => $message->fresh(),
            'success' => true,
        ]);
    }

    /**
     * Get replies to a message.
     */
    public function getReplies(Message $message): JsonResponse
    {
        $replies = $message->replies()->with(['user', 'files'])->get();

        return response()->json(['replies' => $replies]);
    }

    /**
     * Store a reply to a message.
     */
    public function storeReply(StoreMessageRequest $request, Message $message): JsonResponse
    {
        $reply = $this->chatService->createMessage([
            'conversation_id' => $message->conversation_id,
            'user_id' => auth()->id(),
            'content' => $request->content,
            'type' => $request->type ?? 'text',
            'parent_id' => $message->id,
            'metadata' => $request->metadata,
        ]);

        return response()->json([
            'reply' => $reply->load(['user', 'files']),
            'success' => true,
        ]);
    }

    /**
     * Pin a message.
     */
    public function pinMessage(Message $message): JsonResponse
    {
        // Check if user is admin or moderator
        if (!$message->conversation->isModerator(auth()->id())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->chatService->pinMessage($message->id, auth()->id());

        return response()->json(['success' => true]);
    }

    /**
     * Unpin a message.
     */
    public function unpinMessage(Message $message): JsonResponse
    {
        // Check if user is admin or moderator
        if (!$message->conversation->isModerator(auth()->id())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->chatService->unpinMessage($message->id);

        return response()->json(['success' => true]);
    }

    /**
     * Start typing indicator.
     */
    public function startTyping(Conversation $conversation): JsonResponse
    {
        event(new TypingStarted($conversation->id, auth()->id()));

        return response()->json(['success' => true]);
    }

    /**
     * Stop typing indicator.
     */
    public function stopTyping(Conversation $conversation): JsonResponse
    {
        event(new TypingStopped($conversation->id, auth()->id()));

        return response()->json(['success' => true]);
    }

    /**
     * Mark a message as read.
     */
    public function markAsRead(Message $message): JsonResponse
    {
        $this->chatService->markMessageAsRead($message->id, auth()->id());

        return response()->json(['success' => true]);
    }

    /**
     * Search messages and conversations.
     */
    public function search(Request $request): Response
    {
        $query = $request->get('q');
        $results = $this->chatService->search($query, auth()->id());

        return Inertia::render('Chat/Search', [
            'query' => $query,
            'results' => $results,
        ]);
    }

    /**
     * Display chat settings.
     */
    public function settings(): Response
    {
        $userSettings = $this->chatService->getUserSettings(auth()->id());

        return Inertia::render('Chat/Settings', [
            'settings' => $userSettings,
        ]);
    }

    /**
     * Update chat settings.
     */
    public function updateSettings(Request $request): JsonResponse
    {
        $request->validate([
            'notifications' => 'array',
            'theme' => 'string|in:light,dark,system',
            'language' => 'string|in:en,es,fr,de,ar,zh',
        ]);

        $this->chatService->updateUserSettings(auth()->id(), $request->all());

        return response()->json(['success' => true]);
    }

    /**
     * Create a poll.
     */
    public function createPoll(Request $request, Conversation $conversation): JsonResponse
    {
        $request->validate([
            'question' => 'required|string|max:500',
            'options' => 'required|array|min:2|max:10',
            'options.*' => 'string|max:100',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $poll = $this->chatService->createPoll($conversation->id, auth()->id(), $request->all());

        return response()->json([
            'poll' => $poll,
            'success' => true,
        ]);
    }

    /**
     * Vote on a poll.
     */
    public function votePoll(Request $request, $pollId): JsonResponse
    {
        $request->validate([
            'option' => 'required|integer|min:0',
        ]);

        $this->chatService->votePoll($pollId, auth()->id(), $request->option);

        return response()->json(['success' => true]);
    }

    /**
     * Schedule a message.
     */
    public function scheduleMessage(Request $request, Conversation $conversation): JsonResponse
    {
        $request->validate([
            'content' => 'required|string|max:5000',
            'scheduled_at' => 'required|date|after:now',
            'type' => 'string|in:text,image,file',
        ]);

        $message = $this->chatService->scheduleMessage([
            'conversation_id' => $conversation->id,
            'user_id' => auth()->id(),
            'content' => $request->content,
            'type' => $request->type ?? 'text',
            'scheduled_at' => $request->scheduled_at,
        ]);

        return response()->json([
            'message' => $message,
            'success' => true,
        ]);
    }

    /**
     * Cancel a scheduled message.
     */
    public function cancelScheduledMessage(Message $message): JsonResponse
    {
        if ($message->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->chatService->cancelScheduledMessage($message->id);

        return response()->json(['success' => true]);
    }
}
