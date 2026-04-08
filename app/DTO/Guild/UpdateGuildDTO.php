<?php

namespace App\DTO\Guild;

readonly class UpdateGuildDTO
{
    public function __construct(
        public string $name,
        public ?string $description,
        public string $game,
        public bool $is_public,
        public string $dkp_currency_name,
        public ?string $discord_webhook_url,
        public ?int $discord_advance_notice_minutes,
    ) {}
}
