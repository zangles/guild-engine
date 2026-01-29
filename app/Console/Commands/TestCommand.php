<?php

namespace App\Console\Commands;

use GuildEngine\Actions\Guilds\CreateGuildAction;
use GuildEngine\DTOs\CreateGuildDTO;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $data = CreateGuildDTO::make('hola2', 'mundo', 1);
        $action = app(CreateGuildAction::class);

        $a = $action->handle($data);

        dd($a);

    }
}
