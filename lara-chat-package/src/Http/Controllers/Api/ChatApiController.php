<?php

namespace LaraChat\ChatPackage\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use LaraChat\ChatPackage\Models\Conversation;
use LaraChat\ChatPackage\Models\Message;
use LaraChat\ChatPackage\Services\ChatService;

class ChatApiController extends Controller
{
    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Get user conversations
     */
    public function conversations(Request $request): JsonResponse
    {
        $conversations = $this->chatService->getUserConversations(auth()->id());
        
        return response()->json([
            'success' => true,
            'data' => $conversations
        ]);
    }

    /**
     * Get conversation messages
     */
    public function messages(Conversation $conversation): JsonResponse
    {
        $messages = $conversation->messages()
            ->with(['user', 'files'])
            ->latest()
            ->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }

    /**
     * Send a message
     */
    public function sendMessage(Request $request, Conversation $conversation): JsonResponse
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'type' => 'string|in:text,file,image'
        ]);

        $message = $this->chatService->createMessage([
            'conversation_id' => $conversation->id,
            'user_id' => auth()->id(),
            'content' => $request->content,
            'type' => $request->type ?? 'text'
        ]);

        return response()->json([
            'success' => true,
            'data' => $message->load(['user', 'files'])
        ]);
    }

    /**
     * Get typing indicators
     */
    public function typingStatus(Conversation $conversation): JsonResponse
    {
        $typingUsers = $conversation->typingIndicators()
            ->with('user:id,name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $typingUsers
        ]);
    }

    /**
     * Start typing indicator
     */
    public function startTyping(Request $request, Conversation $conversation): JsonResponse
    {
        $this->chatService->startTyping($conversation->id, auth()->id());

        return response()->json(['success' => true]);
    }

    /**
     * Stop typing indicator
     */
    public function stopTyping(Request $request, Conversation $conversation): JsonResponse
    {
        $this->chatService->stopTyping($conversation->id, auth()->id());

        return response()->json(['success' => true]);
    }
}
