<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guild_role_permissions', function (Blueprint $table) {
            $table->foreignId('guild_role_id')->constrained('guild_roles')->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->primary(['guild_role_id', 'permission_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guild_role_permissions');
    }
};
