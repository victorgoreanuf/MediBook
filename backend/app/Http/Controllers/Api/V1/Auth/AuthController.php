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
use Illuminate\Validation\ValidationException;

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
            'message' => 'User registered successfully. Please check your email.',
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
     * @throws ValidationException
     */
    public function verify(Request $request, string $id, string $hash): JsonResponse
    {
        $this->userService->verifyEmail($id, $hash);

        return response()->json([
            'message' => 'Email verified successfully.'
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function resendVerification(Request $request): JsonResponse
    {
        $this->userService->resendVerification($request->user());

        return response()->json([
            'message' => 'Verification link sent.'
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->userService->logout($request->user());

        return response()->json([
            'message' => 'Logged out successfully.'
        ]);
    }
}
