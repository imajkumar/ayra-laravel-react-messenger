<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('message_id');
            $table->unsignedBigInteger('user_id');
            $table->string('filename');
            $table->string('original_filename');
            $table->string('mime_type');
            $table->string('extension');
            $table->bigInteger('size'); // File size in bytes
            $table->string('path'); // Storage path
            $table->string('disk')->default('local');
            $table->json('metadata')->nullable(); // File metadata
            $table->boolean('is_processed')->default(false);
            $table->json('thumbnails')->nullable(); // For images/videos
            $table->timestamps();

            $table->foreign('message_id')->references('id')->on('chat_messages')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['mime_type', 'extension']);
            $table->index('size');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_files');
    }
};
