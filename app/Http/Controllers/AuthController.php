<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RefreshTokenRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->register($request->validated());

        return response()->json([
            'message' => 'Register successfully.',
            'data' => $user,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $results = $this->authService->login($request->validated());

        return response()->json([
            'message' => 'Login successfully.',
            'data' => $results,
        ]);
    }

    public function refreshToken(RefreshTokenRequest $request): JsonResponse
    {
        $results = $this->authService->refreshToken($request->validated('refresh_token'));

        return response()->json([
            'message' => 'Token refreshed successfully.',
            'data' => $results,
        ]);
    }
}
