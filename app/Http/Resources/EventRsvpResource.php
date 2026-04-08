<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventRsvpResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'event_id'   => $this->event_id,
            'user_id'    => $this->user_id,
            'response'   => $this->response,
            'attended'   => $this->attended,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
