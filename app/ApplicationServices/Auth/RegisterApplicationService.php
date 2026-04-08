<?php

namespace App\ApplicationServices\Auth;

use App\DTO\User\CreateUserDTO;
use App\UseCases\Auth\RegisterUserProcess;
use Illuminate\Support\Facades\DB;

class RegisterApplicationService
{
    public function __construct(private RegisterUserProcess $process) {}

    public function handle(CreateUserDTO $dto): array
    {
        $user = DB::transaction(fn () => $this->process->execute($dto));

        $token = $user->createToken('auth_token')->accessToken;

        return compact('user', 'token');
    }
}
