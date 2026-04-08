<?php

namespace App\Http\Controllers;

use App\ApplicationServices\Auth\RegisterApplicationService;
use App\DTO\User\CreateUserDTO;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        private RegisterApplicationService $registerService,
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $dto = new CreateUserDTO(
            name:     $request->name,
            email:    $request->email,
            password: $request->password,
        );

        $result = $this->registerService->handle($dto);

        return response()->json([
            'user'  => new UserResource($result['user']),
            'token' => $result['token'],
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        $user  = Auth::user();
        $token = $user->createToken('auth_token')->accessToken;

        return response()->json([
            'user'  => new UserResource($user),
            'token' => $token,
        ]);
    }

    public function logout(): JsonResponse
    {
        auth()->user()->token()->revoke();
        return response()->json(['message' => 'Logged out successfully.']);
    }
}
