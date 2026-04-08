<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DkpTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'guild_id'       => $this->guild_id,
            'target_user_id' => $this->target_user_id,
            'actor_user_id'  => $this->actor_user_id,
            'amount'         => $this->amount,
            'balance_after'  => $this->balance_after,
            'reason'         => $this->reason,
            'created_at'     => $this->created_at,
            'actor'          => new UserResource($this->whenLoaded('actor')),
        ];
    }
}
