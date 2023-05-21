<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserService
{
    public function createUser($data)
    {
        $user = User::create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => Hash::make($data->password)
        ]);

        return $user;
    }

    public function createUserToken(User $user)
    {
        return $user->createToken('auth_token', [], Carbon::now()->addSeconds(config('auth.token_expiration')));
    }

    public function findUserByEmail($email)
    {
        return User::where('email', $email)->firstOrFail();
    }

    public function isAuthorized($data)
    {
        return Auth::attempt($data->only('email', 'password'));
    }

    public function deleteUserTokenById($user, $tokenId)
    {
        $user->tokens()->where('id', $tokenId)->delete();
    }

    public function deleteAllUserTokens($user)
    {
        $user->tokens()->delete();
    }
}
