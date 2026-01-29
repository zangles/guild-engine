<?php

namespace GuildEngine\DTOs;

final class CreateGuildDTO
{
    public string $name;

    public int $user_id;

    public function __construct(string $name, int $userId)
    {
        $this->name = $name;
        $this->user_id = $userId;
    }

    public static function make(string $name, int $userId): self
    {
        return new self($name, $userId);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'user_id' => $this->user_id,
        ];
    }
}
