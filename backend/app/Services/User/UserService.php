<?php

namespace App\Services\User;

use App\DTOs\User\RegisterUserDTO;
use App\Models\User\User;
use App\Exceptions\Auth\InvalidCredentialsException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Verified;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function createUser(RegisterUserDTO $dto): User
    {
        return User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
            'is_doctor' => false,
        ]);
    }

    /**
     * @throws InvalidCredentialsException
     */
    public function authenticate(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw new InvalidCredentialsException();
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    /**
     * @throws ValidationException
     */
    public function verifyEmail(string $userId, string $hash): void
    {
        $user = User::findOrFail($userId);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            throw ValidationException::withMessages(['message' => 'Invalid verification link.']);
        }

        if ($user->hasVerifiedEmail()) {
            return;
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }
    }

    /**
     * @throws ValidationException
     */
    public function resendVerification(User $user): void
    {
        if ($user->hasVerifiedEmail()) {
            throw ValidationException::withMessages(['message' => 'Email already verified.']);
        }

        $user->sendEmailVerificationNotification();
    }
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
