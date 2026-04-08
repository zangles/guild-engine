<?php

namespace App\Actions\User;

use App\DTO\User\CreateUserDTO;
use App\Models\Main\User;
use App\Repositories\UserRepository;

class CreateUserAction
{
    public function __construct(private UserRepository $repository) {}

    public function handle(CreateUserDTO $dto): User
    {
        return $this->repository->create([
            'name'     => $dto->name,
            'email'    => $dto->email,
            'password' => $dto->password,
        ]);
    }
}
