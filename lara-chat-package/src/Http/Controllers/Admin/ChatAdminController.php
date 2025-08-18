<?php

namespace LaraChat\ChatPackage\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use LaraChat\ChatPackage\Models\Conversation;
use LaraChat\ChatPackage\Models\Message;
use LaraChat\ChatPackage\Models\User;

class ChatAdminController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_conversations' => Conversation::count(),
            'total_messages' => Message::count(),
            'total_users' => User::count(),
        ];

        return Inertia::render('Admin/Dashboard', compact('stats'));
    }

    /**
     * Display all conversations
     */
    public function conversations()
    {
        $conversations = Conversation::with(['creator', 'participants.user', 'lastMessage'])
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Conversations', compact('conversations'));
    }

    /**
     * Display all users
     */
    public function users()
    {
        $users = User::withCount(['conversations', 'messages'])
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Users', compact('users'));
    }

    /**
     * Display all messages
     */
    public function messages()
    {
        $messages = Message::with(['conversation', 'user'])
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Messages', compact('messages'));
    }

    /**
     * Delete a conversation
     */
    public function deleteConversation(Conversation $conversation)
    {
        $conversation->delete();

        return redirect()->back()->with('success', 'Conversation deleted successfully');
    }

    /**
     * Delete a message
     */
    public function deleteMessage(Message $message)
    {
        $message->delete();

        return redirect()->back()->with('success', 'Message deleted successfully');
    }
}
