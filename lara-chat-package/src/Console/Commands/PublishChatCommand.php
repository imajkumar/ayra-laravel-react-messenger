<?php

namespace LaraChat\ChatPackage\Console\Commands;

use Illuminate\Console\Command;

class PublishChatCommand extends Command
{
    protected $signature = 'chat:publish {--tag= : Tag to publish} {--force : Overwrite existing files}';
    protected $description = 'Publish chat package assets';

    public function handle(): int
    {
        $tag = $this->option('tag');
        $force = $this->option('force');

        if ($tag) {
            $this->info("📦 Publishing chat package assets with tag: {$tag}");
            $this->call('vendor:publish', [
                '--provider' => 'LaraChat\ChatPackage\ChatPackageServiceProvider',
                '--tag' => $tag,
                '--force' => $force,
            ]);
        } else {
            $this->info('📦 Publishing all chat package assets...');
            $this->call('vendor:publish', [
                '--provider' => 'LaraChat\ChatPackage\ChatPackageServiceProvider',
                '--force' => $force,
            ]);
        }

        $this->info('✅ Chat package assets published successfully!');
        
        if (!$tag) {
            $this->info('');
            $this->info('Available tags:');
            $this->info('  chat          - All assets');
            $this->info('  chat-config   - Configuration files only');
            $this->info('  chat-migrations - Database migrations only');
            $this->info('');
            $this->info('Example: php artisan chat:publish --tag=chat-config');
        }

        return 0;
    }
}
