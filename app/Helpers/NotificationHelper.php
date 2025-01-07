<?php

namespace App\Helpers;

use App\Notifications\GeneralNotification;

class NotificationHelper
{
    public static function send($notifiable, $title, $message, $data = [])
    {
        $notifiable->notify(new GeneralNotification($title, $message, $data));
    }
}