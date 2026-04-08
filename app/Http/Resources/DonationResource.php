<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DonationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'guild_id'             => $this->guild_id,
            'user_id'              => $this->user_id,
            'amount'               => $this->amount,
            'note'                 => $this->note,
            'status'               => $this->status,
            'reviewed_by_user_id'  => $this->reviewed_by_user_id,
            'reviewed_at'          => $this->reviewed_at,
            'created_at'           => $this->created_at,
            'updated_at'           => $this->updated_at,
            'donor'                => new UserResource($this->whenLoaded('donor')),
        ];
    }
}
