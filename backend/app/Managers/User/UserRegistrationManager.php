<?php

namespace App\Managers\User;

use App\DTOs\User\RegisterUserDTO;
use App\Models\User\User;
use App\Services\User\UserService;
use Illuminate\Support\Facades\DB;
use Exception;

class UserRegistrationManager
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    public function register(RegisterUserDTO $dto): array
    {
        DB::beginTransaction();
        try {
            // 1. Create the User
            $user = $this->userService->createUser($dto);

            // 2. Generate API Token (Laravel Sanctum)
            $token = $user->createToken('auth_token')->plainTextToken;

            // 3. Commit
            DB::commit();

            return ['user' => $user, 'token' => $token];

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
