<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use LaraChat\ChatPackage\Http\Controllers\ChatController;
use LaraChat\ChatPackage\Http\Controllers\Admin\ChatAdminController;
use LaraChat\ChatPackage\Http\Controllers\Api\ChatApiController;

// Original Laravel Routes
Route::get('/', function () {
    return Inertia::render('welcome');
});

Route::get('/dashboard', function () {
    return Inertia::render('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/profile', function () {
    return Inertia::render('profile.edit');
})->middleware('auth')->name('profile.edit');

// Include authentication routes
require __DIR__.'/auth.php';

// Chat Package Routes - User Chat Routes
Route::prefix(config('chat.routes.prefix'))
    ->middleware(config('chat.routes.middleware'))
    ->name('chat.')
    ->group(function () {
        
        // Main chat interface
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('/conversations', [ChatController::class, 'conversations'])->name('conversations');
        Route::get('/conversations/{conversation}', [ChatController::class, 'showConversation'])->name('conversation.show');
        
        // Messages
        Route::post('/conversations/{conversation}/messages', [ChatController::class, 'storeMessage'])->name('message.store');
        Route::put('/messages/{message}', [ChatController::class, 'updateMessage'])->name('message.update');
        Route::delete('/messages/{message}', [ChatController::class, 'deleteMessage'])->name('message.delete');
        
        // File uploads
        Route::post('/upload', [ChatController::class, 'uploadFile'])->name('file.upload');
        Route::delete('/files/{file}', [ChatController::class, 'deleteFile'])->name('file.delete');
        
        // Reactions
        Route::post('/messages/{message}/reactions', [ChatController::class, 'addReaction'])->name('reaction.add');
        Route::delete('/messages/{message}/reactions/{emoji}', [ChatController::class, 'removeReaction'])->name('reaction.remove');
        
        // Threaded conversations
        Route::get('/messages/{message}/replies', [ChatController::class, 'getReplies'])->name('message.replies');
        Route::post('/messages/{message}/replies', [ChatController::class, 'storeReply'])->name('message.reply.store');
        
        // Pinned messages
        Route::post('/messages/{message}/pin', [ChatController::class, 'pinMessage'])->name('message.pin');
        Route::delete('/messages/{message}/unpin', [ChatController::class, 'unpinMessage'])->name('message.unpin');
        
        // Typing indicators
        Route::post('/conversations/{conversation}/typing', [ChatController::class, 'startTyping'])->name('typing.start');
        Route::delete('/conversations/{conversation}/typing', [ChatController::class, 'stopTyping'])->name('typing.stop');
        
        // Read receipts
        Route::post('/messages/{message}/read', [ChatController::class, 'markAsRead'])->name('message.read');
        
        // Search
        Route::get('/search', [ChatController::class, 'search'])->name('search');
        
        // Settings
        Route::get('/settings', [ChatController::class, 'settings'])->name('settings');
        Route::put('/settings', [ChatController::class, 'updateSettings'])->name('settings.update');
        
        // Polls and surveys
        Route::post('/conversations/{conversation}/polls', [ChatController::class, 'createPoll'])->name('poll.create');
        Route::post('/polls/{poll}/vote', [ChatController::class, 'votePoll'])->name('poll.vote');
        
        // Message scheduling
        Route::post('/conversations/{conversation}/schedule', [ChatController::class, 'scheduleMessage'])->name('message.schedule');
        Route::delete('/messages/{message}/schedule', [ChatController::class, 'cancelScheduledMessage'])->name('message.schedule.cancel');
    });

// Admin Chat Routes
Route::prefix(config('chat.routes.admin_prefix'))
    ->middleware(config('chat.routes.admin_middleware'))
    ->name('chat.admin.')
    ->group(function () {
        
        // Dashboard
        Route::get('/', [ChatAdminController::class, 'dashboard'])->name('dashboard');
        
        // Conversations management
        Route::get('/conversations', [ChatAdminController::class, 'conversations'])->name('conversations.index');
        Route::get('/conversations/{conversation}', [ChatAdminController::class, 'showConversation'])->name('conversations.show');
        Route::put('/conversations/{conversation}', [ChatAdminController::class, 'updateConversation'])->name('conversations.update');
        Route::delete('/conversations/{conversation}', [ChatAdminController::class, 'deleteConversation'])->name('conversations.delete');
        
        // Users management
        Route::get('/users', [ChatAdminController::class, 'users'])->name('users.index');
        Route::get('/users/{user}', [ChatAdminController::class, 'showUser'])->name('users.show');
        Route::put('/users/{user}/ban', [ChatAdminController::class, 'banUser'])->name('users.ban');
        Route::put('/users/{user}/unban', [ChatAdminController::class, 'unbanUser'])->name('users.unban');
        
        // Messages management
        Route::get('/messages', [ChatAdminController::class, 'messages'])->name('messages.index');
        Route::delete('/messages/{message}', [ChatAdminController::class, 'deleteMessage'])->name('messages.delete');
        Route::put('/messages/{message}/moderate', [ChatAdminController::class, 'moderateMessage'])->name('messages.moderate');
        
        // Reports and moderation
        Route::get('/reports', [ChatAdminController::class, 'reports'])->name('reports.index');
        Route::get('/reports/{report}', [ChatAdminController::class, 'showReport'])->name('reports.show');
        Route::put('/reports/{report}/resolve', [ChatAdminController::class, 'resolveReport'])->name('reports.resolve');
        
        // Analytics
        Route::get('/analytics', [ChatAdminController::class, 'analytics'])->name('analytics');
        Route::get('/analytics/conversations', [ChatAdminController::class, 'conversationAnalytics'])->name('analytics.conversations');
        Route::get('/analytics/users', [ChatAdminController::class, 'userAnalytics'])->name('analytics.users');
        
        // Settings
        Route::get('/settings', [ChatAdminController::class, 'settings'])->name('settings');
        Route::put('/settings', [ChatAdminController::class, 'updateSettings'])->name('settings.update');
        
        // System health
        Route::get('/health', [ChatAdminController::class, 'systemHealth'])->name('health');
        Route::post('/health/cleanup', [ChatAdminController::class, 'cleanup'])->name('health.cleanup');
    });

// API Routes for real-time features
Route::prefix('api/chat')
    ->middleware(['api', 'auth:sanctum'])
    ->name('chat.api.')
    ->group(function () {
        
        // Real-time events
        Route::post('/events', [ChatApiController::class, 'handleEvent'])->name('events');
        
        // WebSocket authentication
        Route::post('/auth', [ChatApiController::class, 'authenticateSocket'])->name('socket.auth');
        
        // Typing indicators
        Route::post('/typing', [ChatApiController::class, 'typing'])->name('typing');
        
        // Online status
        Route::get('/online', [ChatApiController::class, 'getOnlineUsers'])->name('online');
        Route::post('/online', [ChatApiController::class, 'setOnlineStatus'])->name('online.set');
    });
