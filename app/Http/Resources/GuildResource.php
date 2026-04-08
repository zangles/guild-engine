<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuildResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                             => $this->id,
            'name'                           => $this->name,
            'description'                    => $this->description,
            'game'                           => $this->game,
            'is_public'                      => $this->is_public,
            'leader_user_id'                 => $this->leader_user_id,
            'dkp_currency_name'              => $this->dkp_currency_name,
            'discord_webhook_url'            => $this->discord_webhook_url,
            'discord_advance_notice_minutes' => $this->discord_advance_notice_minutes,
            'created_at'                     => $this->created_at,
            'updated_at'                     => $this->updated_at,
        ];
    }
}
