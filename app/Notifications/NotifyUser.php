<?php

namespace TradeAppOne\Notifications;

use Illuminate\Notifications\Notification;
use TradeAppOne\Domain\Models\Tables\User;

class NotifyUser
{
    public function sendNotification(User $user, Notification $notificationType)
    {
        $user->notify($notificationType);
    }
}
