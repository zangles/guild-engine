<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'guild_id'       => $this->guild_id,
            'actor_user_id'  => $this->actor_user_id,
            'target_user_id' => $this->target_user_id,
            'event_type'     => $this->event_type,
            'payload'        => $this->payload,
            'created_at'     => $this->created_at,
        ];
    }
}
