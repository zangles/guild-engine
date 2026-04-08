<?php

namespace App\ApplicationServices\GuildMember;

use App\Models\Main\Guild;
use App\UseCases\GuildMember\TransferLeadershipProcess;
use Illuminate\Support\Facades\DB;

class TransferLeadershipApplicationService
{
    public function __construct(private TransferLeadershipProcess $process) {}

    public function handle(Guild $guild, int $newLeaderUserId): Guild
    {
        return DB::transaction(fn () => $this->process->execute($guild, $newLeaderUserId));
    }
}
