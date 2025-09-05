<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Event;

class EventPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function view(?User $user, Event $event): bool
    {
        if ($event->status === 'approved') return true;
        if (! $user) return false;
        return $event->submitted_by === $user->id || $user->is_admin;
    }

    public function create(User $user): bool
    {
        return (bool) $user->contributor?->isActive();
    }

    public function update(User $user, Event $event): bool
    {
        if ($user->is_admin) return true;
        return $event->submitted_by === $user->id && in_array($event->status, ['draft', 'rejected'], true);
    }

    public function delete(User $user, Event $event): bool
    {
        if ($event->status === 'approved') return $user->is_admin;
        return $event->submitted_by === $user->id || $user->is_admin;
    }

    public function submit(User $user, Event $event): bool
    {
        return $event->submitted_by === $user->id || $user->is_admin;
    }

    public function approve(User $user, Event $event): bool
    {
        return $user->is_admin;
    }

    public function reject(User $user, Event $event): bool
    {
        return $user->is_admin;
    }
}
