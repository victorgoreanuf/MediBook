<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Managers\User\UserRegistrationManager;
use App\Services\User\UserService;
use App\DTOs\User\RegisterUserDTO;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
// use Illuminate\Auth\Events\Registered; // <--- You can remove this now

class AuthController extends Controller
{
    public function __construct(
        private readonly UserRegistrationManager $registrationManager,
        private readonly UserService $userService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $dto = RegisterUserDTO::fromRequest($request->validated());

        // 1. Create the user
        $result = $this->registrationManager->register($dto);

        // 2. 🚨 DIRECT TRIGGER 🚨
        // Instead of firing an event and hoping a listener catches it,
        // we explicitly tell the user model to queue the email.
        // We know this method works because we tested it in Tinker.
        $result['user']->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'User registered successfully. Please check your email.',
            'data' => [
                'user' => new UserResource($result['user']),
                'token' => $result['token']
            ]
        ], 201);
    }

    // ... keep the rest of the methods (login, logout, verify, resend) exactly as they are ...
    public function resendVerification(Request $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 400);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link sent.']);
    }

    public function verify(EmailVerificationRequest $request): JsonResponse
    {
        $request->fulfill();
        return response()->json(['message' => 'Email verified successfully.']);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->userService->authenticate($request->email, $request->password);
        return response()->json(['message' => 'Login successful.', 'data' => ['user' => new UserResource($result['user']), 'token' => $result['token']]]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully.']);
    }
}
