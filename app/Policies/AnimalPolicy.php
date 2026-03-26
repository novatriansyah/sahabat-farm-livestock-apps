<?php

namespace App\Policies;

use App\Models\Animal;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AnimalPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability)
    {
        if ($user->role === 'PEMILIK') {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Animal $animal): bool
    {
        if ($user->role === 'MITRA') {
            return $user->partner_id === $animal->partner_id;
        }

        return in_array($user->role, ['PEMILIK', 'PETERNAK']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['PEMILIK', 'PETERNAK']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Animal $animal): bool
    {
        if ($user->role === 'MITRA') {
            return false;
        }

        return in_array($user->role, ['PEMILIK', 'PETERNAK']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Animal $animal): bool
    {
        return $user->role === 'PEMILIK';
    }
}
