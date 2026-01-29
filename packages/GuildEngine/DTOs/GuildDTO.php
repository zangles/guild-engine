<?php

namespace GuildEngine\DTOs;

use GuildEngine\Models\Guild;

class GuildDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $description,
        public readonly string $ownerId,
    ) {}

    public static function fromModel(Guild $guild): GuildDTO
    {
        return new self(
            id: $guild->id,
            name: $guild->name,
            description: $guild->description,
            ownerId: $guild->user_id
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'user_id' => $this->ownerId,
        ];
    }
}
