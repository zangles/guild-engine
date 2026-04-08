<?php

namespace App\Actions\AuditLog;

use App\Models\Main\AuditLog;
use App\Repositories\AuditLogRepository;

class CreateAuditLogAction
{
    public function __construct(private AuditLogRepository $repository) {}

    public function handle(
        int $guildId,
        int $actorUserId,
        ?int $targetUserId,
        string $eventType,
        array $payload
    ): AuditLog {
        return $this->repository->create([
            'guild_id'       => $guildId,
            'actor_user_id'  => $actorUserId,
            'target_user_id' => $targetUserId,
            'event_type'     => $eventType,
            'payload'        => $payload,
        ]);
    }
}
