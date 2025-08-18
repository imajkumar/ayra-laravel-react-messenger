<?php

namespace LaraChat\ChatPackage\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class InstallChatCommand extends Command
{
    protected $signature = 'chat:install {--force : Overwrite existing files}';
    protected $description = 'Install the LaraChat package';

    public function handle(): int
    {
        $this->info('🚀 Installing LaraChat Package...');

        // Check if package is already installed
        if (File::exists(config_path('chat.php')) && !$this->option('force')) {
            if (!$this->confirm('Chat package configuration already exists. Overwrite?')) {
                $this->info('Installation cancelled.');
                return 0;
            }
        }

        try {
            // Publish configuration
            $this->info('📁 Publishing configuration files...');
            $this->call('vendor:publish', [
                '--provider' => 'LaraChat\ChatPackage\ChatPackageServiceProvider',
                '--force' => $this->option('force'),
            ]);

            // Publish migrations
            $this->info('🗄️ Publishing database migrations...');
            $this->call('vendor:publish', [
                '--provider' => 'LaraChat\ChatPackage\ChatPackageServiceProvider',
                '--tag' => 'chat-migrations',
                '--force' => $this->option('force'),
            ]);

            // Run migrations
            $this->info('🔄 Running database migrations...');
            $this->call('migrate');

            // Create storage directories
            $this->info('📂 Creating storage directories...');
            $this->createStorageDirectories();

            // Install frontend dependencies
            $this->info('📦 Installing frontend dependencies...');
            $this->installFrontendDependencies();

            // Build frontend assets
            $this->info('🔨 Building frontend assets...');
            $this->buildFrontendAssets();

            // Create example components
            $this->info('🎨 Creating example components...');
            $this->createExampleComponents();

            $this->info('✅ LaraChat package installed successfully!');
            $this->info('');
            $this->info('🎯 Next steps:');
            $this->info('1. Visit /lara-chat to access the chat interface');
            $this->info('2. Visit /lara-chat-admin for the admin panel');
            $this->info('3. Configure your .env file with chat settings');
            $this->info('4. Start the Socket.io server: node resources/js/socket-server.js');
            $this->info('');
            $this->info('📚 Documentation: https://larachat.com/docs');

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Installation failed: ' . $e->getMessage());
            return 1;
        }
    }

    protected function createStorageDirectories(): void
    {
        $directories = [
            storage_path('app/public/chat-files'),
            storage_path('app/public/chat-avatars'),
            storage_path('app/public/chat-thumbnails'),
        ];

        foreach ($directories as $directory) {
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
                $this->line("Created directory: {$directory}");
            }
        }
    }

    protected function installFrontendDependencies(): void
    {
        $packageJsonPath = resource_path('js/package.json');
        
        if (!File::exists($packageJsonPath)) {
            $this->warn('Frontend package.json not found. Skipping frontend installation.');
            return;
        }

        $this->line('Installing npm dependencies...');
        $this->line('This may take a few minutes...');
        
        // Note: In a real implementation, you might want to use Process or exec
        // For now, we'll just inform the user
        $this->info('Please run: cd resources/js && npm install');
    }

    protected function buildFrontendAssets(): void
    {
        $this->line('Building frontend assets...');
        $this->line('This may take a few minutes...');
        
        // Note: In a real implementation, you might want to use Process or exec
        // For now, we'll just inform the user
        $this->info('Please run: cd resources/js && npm run build');
    }

    protected function createExampleComponents(): void
    {
        $componentsDir = resource_path('js/components/chat');
        
        if (!File::exists($componentsDir)) {
            File::makeDirectory($componentsDir, 0755, true);
        }

        // Create example chat page
        $this->createExamplePage();
        
        // Create example layout
        $this->createExampleLayout();
    }

    protected function createExamplePage(): void
    {
        $pagePath = resource_path('js/Pages/Chat/Index.tsx');
        $pageDir = dirname($pagePath);
        
        if (!File::exists($pageDir)) {
            File::makeDirectory($pageDir, 0755, true);
        }

        if (!File::exists($pagePath) || $this->option('force')) {
            $content = $this->getExamplePageContent();
            File::put($pagePath, $content);
            $this->line("Created example page: {$pagePath}");
        }
    }

    protected function createExampleLayout(): void
    {
        $layoutPath = resource_path('js/Layouts/ChatLayout.tsx');
        
        if (!File::exists($layoutPath) || $this->option('force')) {
            $content = $this->getExampleLayoutContent();
            File::put($layoutPath, $content);
            $this->line("Created example layout: {$layoutPath}");
        }
    }

    protected function getExamplePageContent(): string
    {
        return <<<'TSX'
import React from 'react';
import { Head } from '@inertiajs/react';
import ChatLayout from '@/Layouts/ChatLayout';
import ChatInterface from '@/components/ChatInterface';

interface Props {
  conversations: any[];
  unreadCounts: any[];
  user: any;
}

export default function ChatIndex({ conversations, unreadCounts, user }: Props) {
  return (
    <ChatLayout>
      <Head title="Chat" />
      
      <div className="h-screen flex">
        {/* Sidebar */}
        <div className="w-80 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700">
          <div className="p-4">
            <h2 className="text-lg font-semibold text-gray-900 dark:text-white">
              Conversations
            </h2>
          </div>
          
          <div className="flex-1 overflow-y-auto">
            {conversations.map((conversation) => (
              <div
                key={conversation.id}
                className="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer border-b border-gray-100 dark:border-gray-600"
              >
                <div className="flex items-center space-x-3">
                  <div className="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                    <span className="text-sm font-medium text-gray-600">
                      {conversation.name?.charAt(0) || 'C'}
                    </span>
                  </div>
                  
                  <div className="flex-1 min-w-0">
                    <p className="text-sm font-medium text-gray-900 dark:text-white truncate">
                      {conversation.name || 'Untitled'}
                    </p>
                    <p className="text-sm text-gray-500 dark:text-gray-400 truncate">
                      {conversation.last_message_at ? 'Last message' : 'No messages yet'}
                    </p>
                  </div>
                  
                  {unreadCounts[conversation.id] > 0 && (
                    <span className="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                      {unreadCounts[conversation.id]}
                    </span>
                  )}
                </div>
              </div>
            ))}
          </div>
        </div>
        
        {/* Main Chat Area */}
        <div className="flex-1 bg-gray-50 dark:bg-gray-900">
          <div className="h-full flex items-center justify-center">
            <div className="text-center">
              <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">
                Welcome to LaraChat
              </h3>
              <p className="text-gray-500 dark:text-gray-400">
                Select a conversation to start chatting
              </p>
            </div>
          </div>
        </div>
      </div>
    </ChatLayout>
  );
}
TSX;
    }

    protected function getExampleLayoutContent(): string
    {
        return <<<'TSX'
import React from 'react';
import { Link } from '@inertiajs/react';

interface Props {
  children: React.ReactNode;
}

export default function ChatLayout({ children }: Props) {
  return (
    <div className="min-h-screen bg-gray-50 dark:bg-gray-900">
      {/* Header */}
      <header className="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center h-16">
            <div className="flex items-center">
              <Link href="/" className="text-xl font-bold text-gray-900 dark:text-white">
                LaraChat
              </Link>
            </div>
            
            <nav className="flex space-x-8">
              <Link
                href="/lara-chat"
                className="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 px-3 py-2 text-sm font-medium"
              >
                Chat
              </Link>
              <Link
                href="/lara-chat-admin"
                className="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 px-3 py-2 text-sm font-medium"
              >
                Admin
              </Link>
            </nav>
          </div>
        </div>
      </header>
      
      {/* Main Content */}
      <main className="flex-1">
        {children}
      </main>
    </div>
  );
}
TSX;
    }
}
