<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guilds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('game');
            $table->boolean('is_public')->default(true);
            $table->foreignId('leader_user_id')->constrained('users');
            $table->string('dkp_currency_name')->default('DKP');
            $table->string('discord_webhook_url')->nullable();
            $table->integer('discord_advance_notice_minutes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guilds');
    }
};
