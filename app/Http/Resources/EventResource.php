<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'guild_id'            => $this->guild_id,
            'created_by_user_id'  => $this->created_by_user_id,
            'title'               => $this->title,
            'description'         => $this->description,
            'starts_at'           => $this->starts_at,
            'max_attendees'       => $this->max_attendees,
            'status'              => $this->status,
            'created_at'          => $this->created_at,
            'updated_at'          => $this->updated_at,
        ];
    }
}
