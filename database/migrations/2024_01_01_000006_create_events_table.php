<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guild_id')->constrained('guilds')->cascadeOnDelete();
            $table->foreignId('created_by_user_id')->constrained('users');
            $table->string('title');
            $table->text('description')->nullable();
            $table->datetime('starts_at');
            $table->integer('max_attendees')->nullable();
            $table->string('status')->default('scheduled');
            $table->boolean('discord_notified_creation')->default(false);
            $table->timestamp('discord_reminder_sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
