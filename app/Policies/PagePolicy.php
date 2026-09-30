<?php

namespace App\Policies;

use App\Models\Page;
use App\Models\User;

class PagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['super_admin', 'admin', 'staff']) || $user->can('manage_pages');
    }

    public function view(User $user, Page $page): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['super_admin', 'admin']) || $user->can('manage_pages');
    }

    public function update(User $user, Page $page): bool
    {
        return $user->hasRole(['super_admin', 'admin']) || $user->can('manage_pages');
    }

    public function delete(User $user, Page $page): bool
    {
        return $user->hasRole(['super_admin']) || $user->can('manage_pages');
    }
}
