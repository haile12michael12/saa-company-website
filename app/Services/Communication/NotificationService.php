<?php

namespace App\Services\Communication;

use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;

class NotificationService
{
    public function getUnreadNotifications(User $user): Collection
    {
        return $user->unreadNotifications()->get();
    }

    public function getAllNotifications(User $user, int $limit = 20)
    {
        return $user->notifications()->latest()->take($limit)->get();
    }

    public function markAsRead(User $user, string $notificationId): bool
    {
        $notification = $user->notifications()->where('id', $notificationId)->first();
        if ($notification) {
            $notification->markAsRead();
            return true;
        }
        return false;
    }

    public function markAllAsRead(User $user): void
    {
        $user->unreadNotifications()->update(['read_at' => now()]);
    }

    public function sendNotification(User $user, $notification): void
    {
        $user->notify($notification);
    }
}
