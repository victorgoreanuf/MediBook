<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Exceptions\Auth\InvalidCredentialsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Managers\User\UserRegistrationManager;
use App\Services\User\UserService;
use App\DTOs\User\RegisterUserDTO;
use App\Http\Resources\User\UserResource;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly UserRegistrationManager $registrationManager,
        private readonly UserService $userService
    ) {}

    /**
     * @throws Exception
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $dto = RegisterUserDTO::fromRequest($request->validated());

        $result = $this->registrationManager->register($dto);

        return response()->json([
            'message' => 'User registered successfully.',
            'data' => [
                'user' => new UserResource($result['user']),
                'token' => $result['token']
            ]
        ], 201);
    }

    /**
     * @throws InvalidCredentialsException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->userService->authenticate(
            $request->email,
            $request->password
        );

        return response()->json([
            'message' => 'Login successful.',
            'data' => [
                'user' => new UserResource($result['user']),
                'token' => $result['token']
            ]
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.'
        ]);
    }
}
