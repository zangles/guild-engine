<?php

namespace App\Services;

use App\Actions\User\CreateUserAction;
use App\DTO\User\CreateUserDTO;
use App\Finders\UserFinder;
use App\Models\Main\User;

class UserService
{
    public function __construct(
        private UserFinder $finder,
        private CreateUserAction $createAction,
    ) {}

    public function findById(int $id): ?User
    {
        return $this->finder->findById($id);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->finder->findByEmail($email);
    }

    public function create(CreateUserDTO $dto): User
    {
        return $this->createAction->handle($dto);
    }
}
