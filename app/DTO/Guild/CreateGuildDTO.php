<?php

namespace App\DTO\Guild;

readonly class CreateGuildDTO
{
    public function __construct(
        public string $name,
        public ?string $description,
        public string $game,
        public bool $is_public,
        public int $creator_user_id,
    ) {}
}
