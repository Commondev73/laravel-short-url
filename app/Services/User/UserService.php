<?php

namespace App\Services\User;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserCacheService $cache
    ) {}

    public function create(array $input): User
    {
        if (isset($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        }

        return $this->userRepository->create($input);
    }

    public function findById(int $id): User
    {
        $user = $this->cache->rememberById(
            $id,
            fn () => $this->userRepository->findById($id)?->toArray()
        );

        if ($user === null) {
            throw new NotFoundHttpException('User not found.');
        }

        return $user;
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
