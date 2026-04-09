<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuildRoleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'guild_id'    => $this->guild_id,
            'name'        => $this->name,
            'is_system'   => $this->is_system,
            'permissions' => $this->resource->getPermissionSlugs(),
            'created_at'  => $this->created_at,
        ];
    }
}
