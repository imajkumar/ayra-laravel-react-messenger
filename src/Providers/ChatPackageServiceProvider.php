<?php

namespace LaraChat\ChatPackage\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Blade;
use Illuminate\Foundation\Console\AboutCommand;
use LaraChat\ChatPackage\Console\Commands\InstallChatCommand;
use LaraChat\ChatPackage\Console\Commands\PublishChatCommand;

class ChatPackageServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/chat.php', 'chat');
        
        $this->app->singleton('chat', function ($app) {
            return new \LaraChat\ChatPackage\Services\ChatService();
        });
    }

    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        // Load package resources
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'chat');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        $this->loadJsonTranslationsFrom(__DIR__.'/../../resources/lang');
        
        // Register Blade components
        Blade::componentNamespace('LaraChat\\ChatPackage\\View\\Components', 'chat');
        
        // Register Artisan commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallChatCommand::class,
                PublishChatCommand::class,
            ]);
        }
        
        // Publish package assets
        $this->publishes([
            __DIR__.'/../../config/chat.php' => config_path('chat.php'),
            __DIR__.'/../../resources/views' => resource_path('views/vendor/chat'),
            __DIR__.'/../../resources/lang' => resource_path('lang/vendor/chat'),
            __DIR__.'/../../public/vendor/larachat' => public_path('vendor/larachat'),
        ], 'chat');
        
        // Publish migrations separately
        $this->publishesMigrations([
            __DIR__.'/../../database/migrations' => database_path('migrations')
        ], 'chat-migrations');
        
        // Add package info to about command
        AboutCommand::add('LaraChat Package', fn () => [
            'Version' => '1.0.0',
            'Features' => 'Real-time chat, file sharing, group chats, and more'
        ]);
        
        // Register middleware
        $this->app['router']->aliasMiddleware('chat.auth', \LaraChat\ChatPackage\Http\Middleware\ChatAuthMiddleware::class);
        $this->app['router']->aliasMiddleware('chat.admin', \LaraChat\ChatPackage\Http\Middleware\ChatAdminMiddleware::class);
    }
}
