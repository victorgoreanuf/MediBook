<?php

namespace App\Managers\User;

use App\DTOs\User\RegisterUserDTO;
use App\Services\User\UserService;
use Illuminate\Support\Facades\DB;
use Exception;

readonly class UserRegistrationManager
{
    public function __construct(
        private UserService $userService
    ) {}

    /**
     * @throws Exception
     */
    public function register(RegisterUserDTO $dto): array
    {
        DB::beginTransaction();
        try {
            // 1. Create User via Service
            $user = $this->userService->createUser($dto);

            // 2. Generate Token
            $token = $user->createToken('auth_token')->plainTextToken;

            // 3. Trigger Email Verification (Moved from Controller)
            $user->sendEmailVerificationNotification();

            DB::commit();

            return ['user' => $user, 'token' => $token];

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
