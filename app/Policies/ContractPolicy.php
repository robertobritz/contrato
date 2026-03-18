<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Contract;
use App\Models\User;

class ContractPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Contract $contract): bool
    {
        return $user->id === $contract->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Contract $contract): bool
    {
        return $user->id === $contract->user_id;
    }

    public function delete(User $user, Contract $contract): bool
    {
        return $user->id === $contract->user_id;
    }

    public function restore(User $user, Contract $contract): bool
    {
        return $user->id === $contract->user_id;
    }

    public function forceDelete(User $user, Contract $contract): bool
    {
        return $user->id === $contract->user_id;
    }
}
