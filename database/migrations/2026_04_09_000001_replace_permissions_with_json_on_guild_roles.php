<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guild_roles', function (Blueprint $table) {
            $table->json('permissions')->nullable()->after('is_system');
        });

        Schema::dropIfExists('guild_role_permissions');
        Schema::dropIfExists('permissions');
    }

    public function down(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('guild_role_permissions', function (Blueprint $table) {
            $table->foreignId('guild_role_id')->constrained('guild_roles')->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->primary(['guild_role_id', 'permission_id']);
        });

        Schema::table('guild_roles', function (Blueprint $table) {
            $table->dropColumn('permissions');
        });
    }
};