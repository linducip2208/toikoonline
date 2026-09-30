<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['super_admin', 'admin', 'staff']) || $user->can('view_orders');
    }

    public function view(User $user, Order $order): bool
    {
        if ($order->user_id === $user->id) {
            return true;
        }

        return $this->viewAny($user);
    }

    public function update(User $user, Order $order): bool
    {
        return $user->hasRole(['super_admin', 'admin']) || $user->can('update_orders');
    }

    public function refund(User $user, Order $order): bool
    {
        return $user->hasRole(['super_admin', 'admin']) || $user->can('refund_orders');
    }

    public function cancel(User $user, Order $order): bool
    {
        if ($order->user_id === $user->id && $order->payment_status === 'unpaid') {
            return true;
        }

        return $user->hasRole(['super_admin', 'admin']) || $user->can('cancel_orders');
    }
}
