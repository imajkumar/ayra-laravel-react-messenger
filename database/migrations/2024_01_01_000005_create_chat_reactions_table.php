<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_reactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('message_id');
            $table->unsignedBigInteger('user_id');
            $table->string('emoji'); // Emoji reaction
            $table->timestamps();

            $table->foreign('message_id')->references('id')->on('chat_messages')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['message_id', 'user_id', 'emoji']);
            $table->index(['message_id', 'emoji']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_reactions');
    }
};
