<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VentureMagazine;

class VentureMagazinePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct() {}

    public function view(?User $user, VentureMagazine $post): bool
    {
        if ($post->status === 'approved') return true;
        if (! $user) return false;
        return $post->submitted_by === $user->id || $user->is_admin;
    }

    public function create(User $user): bool
    {
        return (bool) $user->contributor?->isActive();
    }

    public function update(User $user, VentureMagazine $post): bool
    {
        if ($user->is_admin) return true;
        // Owner can directly update only when not approved
        return $post->submitted_by === $user->id && in_array($post->status, ['draft', 'rejected'], true);
    }

    public function delete(User $user, VentureMagazine $post): bool
    {
        if ($post->status === 'approved') return $user->is_admin;
        return $post->submitted_by === $user->id || $user->is_admin;
    }

    public function submit(User $user, VentureMagazine $post): bool
    {
        // Owner can submit for approval anytime
        return $post->submitted_by === $user->id || $user->is_admin;
    }

    public function approve(User $user, VentureMagazine $post): bool
    {
        return $user->is_admin;
    }

    public function reject(User $user, VentureMagazine $post): bool
    {
        return $user->is_admin;
    }
}
