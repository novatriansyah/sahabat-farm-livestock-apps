<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AnimalTask;
use Illuminate\Auth\Access\HandlesAuthorization;

class AnimalTaskPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can complete the task.
     */
    public function complete(User $user, AnimalTask $task): bool
    {
        return in_array($user->role, ['PEMILIK', 'STAF', 'PETERNAK']);
    }

    /**
     * Determine whether the user can view the task.
     */
    public function view(User $user, AnimalTask $task): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create tasks.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['PEMILIK', 'STAF']);
    }

    /**
     * Determine whether the user can update the task.
     */
    public function update(User $user, AnimalTask $task): bool
    {
        return in_array($user->role, ['PEMILIK', 'STAF']);
    }

    /**
     * Determine whether the user can delete the task.
     */
    public function delete(User $user, AnimalTask $task): bool
    {
        return $user->role === 'PEMILIK';
    }
}
