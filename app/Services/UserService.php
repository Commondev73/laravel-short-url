<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function create(array $input): User
    {
        if (isset($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        }

        return $this->userRepository->create($input);
    }

    public function findForLogin(array $credentials): ?User
    {
        if (! empty($credentials['email'])) {
            return $this->userRepository->findEmail($credentials['email']);
        }

        if (! empty($credentials['username'])) {
            return $this->userRepository->findUsername($credentials['username']);
        }

        return null;
    }
}
