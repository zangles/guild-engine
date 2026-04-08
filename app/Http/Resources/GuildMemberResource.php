<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuildMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'guild_id'            => $this->guild_id,
            'user_id'             => $this->user_id,
            'guild_role_id'       => $this->guild_role_id,
            'status'              => $this->status,
            'invited_by_user_id'  => $this->invited_by_user_id,
            'joined_at'           => $this->joined_at,
            'created_at'          => $this->created_at,
            'user'                => new UserResource($this->whenLoaded('user')),
            'role'                => new GuildRoleResource($this->whenLoaded('role')),
        ];
    }
}
