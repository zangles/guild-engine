<?php

namespace GuildEngine\Providers;

use GuildEngine\Repositories\Guilds\GuildsRepository;
use GuildEngine\Repositories\Guilds\GuildsRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class GuildEngineServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(GuildsRepositoryInterface::class, GuildsRepository::class);
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
    }
}
