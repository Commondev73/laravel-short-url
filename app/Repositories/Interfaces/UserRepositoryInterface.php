<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
{
    public function create(array $data): User;

    public function findById(int $id): ?User;
    
    public function findEmail(string $email): ?User;

    public function findUsername(string $username): ?User;
}
