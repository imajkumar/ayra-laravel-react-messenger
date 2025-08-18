<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Chat Package Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration options for the LaraChat package.
    |
    */

    'name' => 'LaraChat',
    'version' => '1.0.0',

    /*
    |--------------------------------------------------------------------------
    | Routes Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the routes for the chat package.
    |
    */

    'routes' => [
        'prefix' => 'lara-chat',
        'admin_prefix' => 'lara-chat-admin',
        'middleware' => ['web', 'auth'],
        'admin_middleware' => ['web', 'auth', 'chat.admin'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the database tables and models.
    |
    */

    'database' => [
        'tables' => [
            'conversations' => 'chat_conversations',
            'messages' => 'chat_messages',
            'participants' => 'chat_participants',
            'reactions' => 'chat_reactions',
            'files' => 'chat_files',
            'pinned_messages' => 'chat_pinned_messages',
            'typing_indicators' => 'chat_typing_indicators',
            'read_receipts' => 'chat_read_receipts',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Configuration
    |--------------------------------------------------------------------------
    |
    | Configure file upload settings for the chat.
    |
    */

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

    /*
    |--------------------------------------------------------------------------
    | Real-time Configuration
    |--------------------------------------------------------------------------
    |
    | Configure real-time features using Socket.io or Pusher.
    |
    */

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

    /*
    |--------------------------------------------------------------------------
    | Chat Features Configuration
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific chat features.
    |
    */

    'features' => [
        'private_chats' => true,
        'group_chats' => true,
        'file_sharing' => true,
        'message_reactions' => true,
        'threaded_conversations' => true,
        'read_receipts' => true,
        'typing_indicators' => true,
        'message_scheduling' => true,
        'message_translation' => true,
        'polls_surveys' => true,
        'ai_assistants' => true,
        'custom_stickers' => true,
        'themes_dark_mode' => true,
        'offline_messaging' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | UI Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the UI components and styling.
    |
    */

    'ui' => [
        'theme' => 'default',
        'components' => [
            'chat_interface' => 'chat::components.chat-interface',
            'message_bubble' => 'chat::components.message-bubble',
            'file_upload' => 'chat::components.file-upload',
            'user_avatar' => 'chat::components.user-avatar',
        ],
        'styling' => [
            'primary_color' => '#3B82F6',
            'secondary_color' => '#6B7280',
            'success_color' => '#10B981',
            'warning_color' => '#F59E0B',
            'error_color' => '#EF4444',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | Configure security settings for the chat.
    |
    */

    'security' => [
        'rate_limiting' => [
            'enabled' => true,
            'max_attempts' => 60,
            'decay_minutes' => 1,
        ],
        'content_filtering' => [
            'enabled' => true,
            'blocked_words' => [],
            'profanity_filter' => true,
        ],
        'file_scanning' => [
            'enabled' => true,
            'antivirus_check' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Configuration
    |--------------------------------------------------------------------------
    |
    | Configure notification settings for the chat.
    |
    */

    'notifications' => [
        'channels' => ['mail', 'database', 'broadcast'],
        'email' => [
            'enabled' => true,
            'template' => 'chat::emails.message-notification',
        ],
        'push' => [
            'enabled' => false,
            'vapid_public_key' => env('VAPID_PUBLIC_KEY'),
            'vapid_private_key' => env('VAPID_PRIVATE_KEY'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Configuration
    |--------------------------------------------------------------------------
    |
    | Configure performance settings for the chat.
    |
    */

    'performance' => [
        'message_pagination' => 50,
        'conversation_pagination' => 20,
        'cache_ttl' => 3600, // 1 hour
        'queue_workers' => 2,
        'websocket_connections' => 1000,
    ],
];
