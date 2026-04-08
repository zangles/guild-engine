<?php

namespace App\ApplicationServices\Guild;

use App\DTO\Guild\CreateGuildDTO;
use App\Models\Main\Guild;
use App\UseCases\Guild\CreateGuildProcess;
use Illuminate\Support\Facades\DB;

class CreateGuildApplicationService
{
    public function __construct(private CreateGuildProcess $process) {}

    public function handle(CreateGuildDTO $dto): Guild
    {
        return DB::transaction(fn () => $this->process->execute($dto));
    }
}
