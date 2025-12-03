<?php

namespace App\Services\User;

use App\DTOs\User\RegisterUserDTO;
use App\Models\User\User;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\Auth\InvalidCredentialsException;

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
     * @param string $email
     * @param string $password
     * @return array{user: User, token: string}
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
}
