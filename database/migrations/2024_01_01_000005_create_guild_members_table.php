<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guild_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guild_id')->constrained('guilds')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('guild_role_id')->constrained('guild_roles');
            $table->string('status');
            $table->foreignId('invited_by_user_id')->nullable()->constrained('users');
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();
            $table->unique(['guild_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guild_members');
    }
};
