<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GeneralNotification extends Notification
{
    use Queueable;

    protected $title;
    protected $message;
    protected $data;

    public function __construct($title, $message, $data = [])
    {
        $this->title = $title;
        $this->message = $message;
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['database']; // Menggunakan notifikasi berbasis database
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'data' => $this->data,
        ];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'data' => $this->data,
        ];
    }
}