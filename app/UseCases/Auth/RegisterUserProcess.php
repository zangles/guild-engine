<?php

namespace App\UseCases\Auth;

use App\DTO\User\CreateUserDTO;
use App\Models\Main\User;
use App\Services\UserService;

class RegisterUserProcess
{
    public function __construct(private UserService $userService) {}

    public function execute(CreateUserDTO $dto): User
    {
        return $this->userService->create($dto);
    }
}
