<?php

namespace App\Repositories;

use App\Models\Main\AuditLog;

class AuditLogRepository
{
    public function create(array $data): AuditLog
    {
        return AuditLog::create($data);
    }
}
