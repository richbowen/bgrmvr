<?php

namespace App\Policies;

use App\Models\BackgroundRemoval;
use App\Models\User;

class BackgroundRemovalPolicy
{
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
    public function view(User $user, BackgroundRemoval $backgroundRemoval): bool
    {
        return $user->id === $backgroundRemoval->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BackgroundRemoval $backgroundRemoval): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BackgroundRemoval $backgroundRemoval): bool
    {
        return $user->id === $backgroundRemoval->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BackgroundRemoval $backgroundRemoval): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BackgroundRemoval $backgroundRemoval): bool
    {
        return false;
    }
}
