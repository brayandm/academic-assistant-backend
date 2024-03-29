<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function createUser($data)
    {
        $user = User::create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => Hash::make($data->password),
        ]);

        if (isset($data->roles)) {
            $roles = Role::whereIn('name', $data->roles)->get();
            $user->roles()->attach($roles);
        }

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

    public function isCredentialsCorrect($data)
    {
        return Auth::attempt($data->only('email', 'password'));
    }

    public function deleteUserTokenById($user, $tokenId)
    {
        $user->tokens()->where('id', $tokenId)->update(['expires_at' => Carbon::now()]);
    }

    public function deleteAllUserTokens($user)
    {
        $user->tokens()->update(['expires_at' => Carbon::now()]);
    }

    public function getPolicies($user)
    {
        return $user->roles->pluck('policies')->flatten()->unique('id')->pluck('name')->toArray();
    }
}
