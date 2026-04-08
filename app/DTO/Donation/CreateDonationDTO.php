<?php

namespace App\DTO\Donation;

readonly class CreateDonationDTO
{
    public function __construct(
        public int $guild_id,
        public int $user_id,
        public int $amount,
        public ?string $note,
    ) {}
}
