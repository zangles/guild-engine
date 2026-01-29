<?php

namespace GuildEngine\DTOs;

final class CreateGuildDTO
{
    public readonly string $name;

    public readonly ?string $description;

    public readonly int $user_id;

    public function __construct(string $name, ?string $description, int $userId)
    {
        $this->name = $name;
        $this->description = $description;
        $this->user_id = $userId;
    }

    public static function make(string $name, ?string $description, int $userId): self
    {
        return new self($name, $description, $userId);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'user_id' => $this->user_id,
        ];
    }
}
