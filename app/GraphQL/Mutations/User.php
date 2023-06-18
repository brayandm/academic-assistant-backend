<?php

namespace App\GraphQL\Mutations;

use App\Services\UserService;

final class User
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function createUser($root, array $args)
    {

        $data = (object) [
            'name' => $args['name'],
            'email' => $args['email'],
            'password' => $args['password'],
            'roles' => $args['roles'],
        ];

        return $this->userService->createUser($data);
    }
}
