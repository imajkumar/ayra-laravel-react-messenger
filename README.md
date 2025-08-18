# 🚀 LaraChat - Comprehensive Laravel Chat Package

A feature-rich, enterprise-grade real-time chat package for Laravel applications with React, Inertia.js, and Socket.io. Built with modern UI components using shadcn/ui and Tailwind CSS, designed to provide a WhatsApp-like experience with advanced collaboration features.

![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)
![React](https://img.shields.io/badge/React-18.x-blue.svg)
![TypeScript](https://img.shields.io/badge/TypeScript-5.x-blue.svg)
![Socket.io](https://img.shields.io/badge/Socket.io-4.x-green.svg)
![License](https://img.shields.io/badge/License-MIT-green.svg)

## 🌟 Features Overview

### 💬 Core Chat Features
- **1:1 Messaging** – Private direct chats with real-time updates
- **Group Chats** – Multiple participants in single chat rooms with role management
- **Message Reactions** – Emoji reactions for quick responses (like WhatsApp)
- **Threaded Conversations** – Reply within threads (MS Teams, Slack style)
- **Read Receipts** – See who has read your messages
- **Typing Indicators** – Real-time "typing..." visibility
- **Message Editing** – Edit sent messages with edit history
- **Message Deletion** – Soft delete with admin override
- **Message Search** – Full-text search across conversations

### 📁 Collaboration Features
- **File Sharing & Storage** – Documents, images, videos, audio files
- **Cloud Integration** – Google Drive, OneDrive, Dropbox ready
- **Inline Previews** – Rich previews for docs, links, videos
- **Pinned Messages** – Important messages stay at the top
- **Searchable Chat History** – Find old messages and files easily
- **AI Chatbots/Assistants** – Reminders, task automation, smart responses
- **Message Scheduling** – Send messages at specific times
- **Translation in Chat** – Built-in multi-language translator
- **Polls & Surveys** – Quick team decisions and feedback
- **Cross-Device Sync** – Seamless mobile, desktop, web experience
- **Offline Messaging** – Queue messages when offline, deliver when connected
- **Custom Stickers & GIFs** – Fun, engaging communication
- **Themes & Dark Mode** – Personalization and accessibility

### 🔧 Technical Features
- **Real-time Communication** – Socket.io with fallback to Pusher
- **Modern UI/UX** – WhatsApp-like interface with shadcn/ui
- **Responsive Design** – Works perfectly on all devices
- **TypeScript Support** – Full type safety and IntelliSense
- **Inertia.js Integration** – Seamless Laravel + React experience
- **Admin Panel** – Comprehensive moderation and analytics tools
- **API Ready** – RESTful API endpoints for mobile apps
- **Event Broadcasting** – Laravel events for real-time updates
- **Queue Processing** – Background job processing for heavy operations
- **Caching** – Redis-based caching for performance
- **Rate Limiting** – Built-in security and spam protection

## 📋 Requirements

- **PHP**: 8.2 or higher
- **Laravel**: 11.x
- **Node.js**: 18 or higher
- **Database**: PostgreSQL 12+ / MySQL 8+ / SQLite 3.35+
- **Redis**: 6.0+ (for caching and queues)
- **WebSocket**: Socket.io server or Pusher account

## 🛠️ Installation

### 1. Install the Package

```bash
composer require larachat/chat-package
```

### 2. Publish Package Assets

```bash
php artisan vendor:publish --provider="LaraChat\ChatPackage\ChatPackageServiceProvider"
```

### 3. Run Migrations

```bash
php artisan migrate
```

### 4. Install Frontend Dependencies

```bash
cd resources/js
npm install
```

### 5. Build Frontend Assets

```bash
npm run build
```

### 6. Configure Environment Variables

Add these to your `.env` file:

```env
# Chat Package Configuration
CHAT_REALTIME_DRIVER=socket.io
CHAT_SOCKET_HOST=localhost
CHAT_SOCKET_PORT=3001
CHAT_FILE_DISK=local
CHAT_MAX_FILE_SIZE=10240

# Pusher (Alternative to Socket.io)
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=your_cluster

# File Storage
FILESYSTEM_DISK=local
CHAT_FILE_DISK=local

# Redis (for caching and queues)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Queue Configuration
QUEUE_CONNECTION=redis
```

### 7. Start Socket.io Server

```bash
node resources/js/socket-server.js
```

## 🎯 Quick Start Guide

### 1. Basic Setup

After installation, the package automatically registers:

- **Routes**: `/lara-chat` and `/lara-chat-admin`
- **Middleware**: `chat.auth` and `chat.admin`
- **Service Provider**: `ChatPackageServiceProvider`
- **Facade**: `Chat` facade for easy access

### 2. Access the Chat Interface

- **User Chat**: Visit `/lara-chat` in your browser
- **Admin Panel**: Visit `/lara-chat-admin` for moderation tools

### 3. Basic Usage Example

```php
use LaraChat\ChatPackage\Services\ChatService;

class ChatController extends Controller
{
    public function index(ChatService $chatService)
    {
        $conversations = $chatService->getUserConversations(auth()->id());
        $unreadCounts = $chatService->getUnreadCounts(auth()->id());
        
        return Inertia::render('Chat/Index', [
            'conversations' => $conversations,
            'unreadCounts' => $unreadCounts
        ]);
    }
}
```

## 🎨 UI Components

### Available Components

The package includes pre-built React components:

```tsx
// Main chat interface
import ChatInterface from '@/components/ChatInterface';

// Individual message display
import MessageBubble from '@/components/MessageBubble';

// File upload component
import FileUpload from '@/components/FileUpload';

// Emoji selection
import EmojiPicker from '@/components/EmojiPicker';

// Typing status
import TypingIndicator from '@/components/TypingIndicator';

// Conversation list
import ConversationList from '@/components/ConversationList';
```

### Component Usage Example

```tsx
import React from 'react';
import ChatInterface from '@/components/ChatInterface';

export default function ChatPage({ conversation, messages, user }) {
  return (
    <div className="h-screen">
      <ChatInterface
        conversation={conversation}
        messages={messages}
        user={user}
        participants={conversation.participants}
      />
    </div>
  );
}
```

## 🔧 Configuration

### Chat Settings

```php
// config/chat.php
'features' => [
    'private_chats' => true,        // Enable 1:1 messaging
    'group_chats' => true,          // Enable group conversations
    'file_sharing' => true,         // Enable file uploads
    'message_reactions' => true,    // Enable emoji reactions
    'threaded_conversations' => true, // Enable message threading
    'read_receipts' => true,        // Enable read receipts
    'typing_indicators' => true,    // Enable typing indicators
    'message_scheduling' => true,   // Enable scheduled messages
    'message_translation' => true,  // Enable translation
    'polls_surveys' => true,        // Enable polls and surveys
    'ai_assistants' => true,        // Enable AI features
    'custom_stickers' => true,      // Enable custom stickers
    'themes_dark_mode' => true,     // Enable themes
    'offline_messaging' => true,    // Enable offline queuing
],
```

### File Upload Settings

```php
'uploads' => [
    'disk' => env('CHAT_FILE_DISK', 'local'),
    'max_size' => env('CHAT_MAX_FILE_SIZE', 10240), // 10MB
    'allowed_types' => [
        'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'document' => ['pdf', 'doc', 'docx', 'txt', 'rtf'],
        'video' => ['mp4', 'avi', 'mov', 'wmv'],
        'audio' => ['mp3', 'wav', 'ogg', 'm4a'],
    ],
    'path' => 'chat-files',
],
```

### Real-time Configuration

```php
'realtime' => [
    'driver' => env('CHAT_REALTIME_DRIVER', 'socket.io'),
    'socket' => [
        'host' => env('CHAT_SOCKET_HOST', 'localhost'),
        'port' => env('CHAT_SOCKET_PORT', 3001),
        'namespace' => '/chat',
    ],
    'pusher' => [
        'app_id' => env('PUSHER_APP_ID'),
        'app_key' => env('PUSHER_APP_KEY'),
        'app_secret' => env('PUSHER_APP_SECRET'),
        'app_cluster' => env('PUSHER_APP_CLUSTER'),
    ],
],
```

## 🚀 Advanced Features

### 1. Real-time Communication

```javascript
import { useSocket } from '@/hooks/useSocket';

const { socket, isConnected } = useSocket();

// Listen for incoming messages
socket.on('message:received', (data) => {
    console.log('New message:', data);
    // Handle new message
});

// Send typing indicator
socket.emit('typing:started', { conversation_id: 1 });

// Stop typing indicator
socket.emit('typing:stopped', { conversation_id: 1 });
```

### 2. File Handling

```php
use LaraChat\ChatPackage\Services\ChatService;

$chatService = app(ChatService::class);

// Upload file
$file = $chatService->uploadFile($request->file('file'), $conversationId);

// Get file URL
$url = $chatService->getFileUrl($file);

// Delete file
$chatService->deleteFile($file);
```

### 3. Message Scheduling

```php
$message = $chatService->scheduleMessage([
    'conversation_id' => $conversationId,
    'user_id' => auth()->id(),
    'content' => 'Hello from the future!',
    'scheduled_at' => now()->addHours(2),
]);

// Cancel scheduled message
$chatService->cancelScheduledMessage($message->id);
```

### 4. Polls and Surveys

```php
$poll = $chatService->createPoll($conversationId, auth()->id(), [
    'question' => 'What should we have for lunch?',
    'options' => ['Pizza', 'Burger', 'Salad', 'Sushi'],
    'expires_at' => now()->addDay(),
]);

// Vote on poll
$chatService->votePoll($poll->id, auth()->id(), 0); // Vote for Pizza
```

### 5. Message Reactions

```php
// Add reaction
$message->addReaction(auth()->id(), '👍');

// Remove reaction
$message->removeReaction(auth()->id(), '👍');

// Get reaction count
$count = $message->getReactionCount('👍');
```

### 6. Threaded Conversations

```php
// Create reply to a message
$reply = $chatService->createMessage([
    'conversation_id' => $conversationId,
    'user_id' => auth()->id(),
    'content' => 'This is a reply',
    'parent_id' => $parentMessage->id,
]);

// Get all replies to a message
$replies = $message->replies()->with(['user', 'files'])->get();
```

## 🎭 Customization

### Custom Themes

```css
/* resources/css/chat.css */
.chat-theme-custom {
    --chat-primary: #6366f1;
    --chat-secondary: #8b5cf6;
    --chat-background: #f8fafc;
    --chat-foreground: #1e293b;
    --chat-accent: #f59e0b;
    --chat-muted: #64748b;
}

.chat-theme-dark {
    --chat-primary: #3b82f6;
    --chat-secondary: #8b5cf6;
    --chat-background: #0f172a;
    --chat-foreground: #f1f5f9;
    --chat-accent: #f59e0b;
    --chat-muted: #475569;
}
```

### Custom Components

```tsx
// resources/js/components/CustomMessageBubble.tsx
import { MessageBubble } from 'larachat-chat-package';

const CustomMessageBubble = ({ message, ...props }) => {
    return (
        <div className="custom-message-wrapper">
            <MessageBubble message={message} {...props} />
            <div className="custom-timestamp">
                {new Date(message.created_at).toLocaleTimeString()}
            </div>
        </div>
    );
};
```

### Custom Middleware

```php
// app/Http/Middleware/CustomChatMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CustomChatMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Custom logic before chat access
        if (!auth()->user()->hasPermission('chat.access')) {
            abort(403, 'Chat access denied');
        }
        
        return $next($request);
    }
}
```

## 🔒 Security Features

### Middleware

```php
// Register custom middleware
Route::middleware(['web', 'auth', 'chat.auth', 'custom.chat'])->group(function () {
    Route::prefix('lara-chat')->group(function () {
        // Chat routes
    });
});
```

### Rate Limiting

```php
'security' => [
    'rate_limiting' => [
        'enabled' => true,
        'max_attempts' => 60,
        'decay_minutes' => 1,
    ],
],
```

### Content Filtering

```php
'content_filtering' => [
    'enabled' => true,
    'blocked_words' => ['spam', 'inappropriate'],
    'profanity_filter' => true,
],
```

### File Security

```php
'file_scanning' => [
    'enabled' => true,
    'antivirus_check' => false,
    'allowed_extensions' => ['jpg', 'png', 'pdf', 'doc'],
],
```

## 📱 Mobile Support

### Progressive Web App (PWA)

The package includes PWA features:

```json
// public/manifest.json
{
    "name": "LaraChat",
    "short_name": "Chat",
    "start_url": "/lara-chat",
    "display": "standalone",
    "background_color": "#ffffff",
    "theme_color": "#3b82f6"
}
```

### Responsive Design

- **Mobile-first** approach
- **Touch-friendly** interface
- **Gesture support** for mobile devices
- **Offline message queuing**
- **Push notifications** (when configured)

## 🧪 Testing

### Package Tests

```bash
# Run package tests
php artisan test --filter=ChatPackage

# Run specific test file
php artisan test tests/Feature/ChatTest.php

# Run with coverage
php artisan test --coverage
```

### Frontend Tests

```bash
# Run React component tests
npm test

# Run tests in watch mode
npm run test:watch

# Run tests with coverage
npm run test:coverage
```

### Example Test

```php
// tests/Feature/ChatTest.php
namespace Tests\Feature;

use Tests\TestCase;
use LaraChat\ChatPackage\Models\Conversation;
use LaraChat\ChatPackage\Models\Message;

class ChatTest extends TestCase
{
    public function test_user_can_send_message()
    {
        $user = User::factory()->create();
        $conversation = Conversation::factory()->create();
        
        $response = $this->actingAs($user)
            ->postJson("/lara-chat/conversations/{$conversation->id}/messages", [
                'content' => 'Hello World!',
                'type' => 'text'
            ]);
        
        $response->assertStatus(200);
        $this->assertDatabaseHas('chat_messages', [
            'content' => 'Hello World!',
            'user_id' => $user->id
        ]);
    }
}
```

## 📊 Performance Optimization

### Caching Strategy

```php
'performance' => [
    'message_pagination' => 50,
    'conversation_pagination' => 20,
    'cache_ttl' => 3600, // 1 hour
    'queue_workers' => 2,
    'websocket_connections' => 1000,
],
```

### Database Optimization

```sql
-- Add indexes for better performance
CREATE INDEX idx_chat_messages_conversation_created 
ON chat_messages(conversation_id, created_at);

CREATE INDEX idx_chat_participants_user_conversation 
ON chat_participants(user_id, conversation_id);
```

### Queue Processing

```bash
# Start queue workers
php artisan queue:work --queue=chat,default

# Monitor queues
php artisan queue:monitor

# Failed job handling
php artisan queue:failed
php artisan queue:retry all
```

## 🔍 Monitoring & Analytics

### System Health

```bash
# Check system health
php artisan chat:health

# View performance metrics
php artisan chat:metrics

# System cleanup
php artisan chat:cleanup
```

### Admin Analytics

```php
// Get conversation statistics
$stats = $chatService->getConversationStats();

// Get user activity
$activity = $chatService->getUserActivity($userId);

// Get file usage statistics
$fileStats = $chatService->getFileUsageStats();
```

## 🚀 Deployment

### Production Setup

```bash
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build frontend assets
npm run build

# Start Socket.io server with PM2
pm2 start socket-server.js --name "larachat-socket"
```

### Environment Configuration

```env
# Production settings
APP_ENV=production
APP_DEBUG=false
CHAT_REALTIME_DRIVER=pusher
CHAT_FILE_DISK=s3
QUEUE_CONNECTION=redis
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

### Docker Support

```dockerfile
# Dockerfile for Socket.io server
FROM node:18-alpine
WORKDIR /app
COPY package*.json ./
RUN npm ci --only=production
COPY . .
EXPOSE 3001
CMD ["node", "socket-server.js"]
```

## 🤝 Contributing

### Development Setup

```bash
# Clone repository
git clone https://github.com/imajkumar/ayra-laravel-react-messenger.git

# Install dependencies
composer install
npm install

# Run tests
php artisan test
npm test

# Build assets
npm run build
```

### Code Standards

- Follow PSR-12 coding standards
- Write comprehensive tests
- Update documentation
- Use conventional commits

### Pull Request Process

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## 📄 License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## 🆘 Support

### Documentation
- **Package Docs**: [https://larachat.com/docs](https://larachat.com/docs)
- **API Reference**: [https://larachat.com/api](https://larachat.com/api)
- **Examples**: [https://larachat.com/examples](https://larachat.com/examples)

### Community
- **GitHub Issues**: [Report bugs](https://github.com/imajkumar/ayra-laravel-react-messenger/issues)
- **Discord**: [Join our community](https://discord.gg/larachat)
- **Stack Overflow**: [Tag: larachat](https://stackoverflow.com/questions/tagged/larachat)

### Professional Support
- **Email**: support@larachat.com
- **Slack**: [Enterprise support](https://larachat.slack.com)
- **Consulting**: [Custom implementations](https://larachat.com/consulting)

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - The PHP framework for web artisans
- [Inertia.js](https://inertiajs.com) - Modern monoliths
- [shadcn/ui](https://ui.shadcn.com) - Beautiful components
- [Socket.io](https://socket.io) - Real-time bidirectional communication
- [Tailwind CSS](https://tailwindcss.com) - Utility-first CSS framework
- [React](https://reactjs.org) - A JavaScript library for building user interfaces

## 📈 Roadmap

### Version 1.1 (Q4 2024)
- [ ] Voice messages support
- [ ] Video calling integration
- [ ] Advanced file previews
- [ ] Message encryption

### Version 1.2 (Q1 2025)
- [ ] Multi-tenant support
- [ ] Advanced analytics dashboard
- [ ] Custom bot framework
- [ ] Mobile app SDKs

### Version 2.0 (Q2 2025)
- [ ] Microservices architecture
- [ ] Advanced AI features
- [ ] Enterprise SSO integration
- [ ] Advanced security features

---

**Made with ❤️ by the LaraChat Team**

*Building the future of real-time communication, one message at a time.*
