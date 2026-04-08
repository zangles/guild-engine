<?php

namespace App\DTO\DkpTransaction;

readonly class DeductDkpDTO
{
    public function __construct(
        public int $guild_id,
        public int $target_user_id,
        public int $actor_user_id,
        public int $amount,
        public string $reason,
    ) {}
}
