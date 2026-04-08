<?php

namespace App\Services;

use App\Actions\AuditLog\CreateAuditLogAction;
use App\Finders\AuditLogFinder;
use App\Models\Main\AuditLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AuditLogService
{
    public function __construct(
        private AuditLogFinder $finder,
        private CreateAuditLogAction $createAction,
    ) {}

    public function log(
        int $guildId,
        int $actorUserId,
        ?int $targetUserId,
        string $eventType,
        array $payload
    ): AuditLog {
        return $this->createAction->handle($guildId, $actorUserId, $targetUserId, $eventType, $payload);
    }

    public function getByGuild(int $guildId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->finder->findByGuild($guildId, $perPage);
    }
}
