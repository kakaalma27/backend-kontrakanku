<?php
namespace App\Events;

use Log;
use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewNotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $title;
    public $message;
    public $imageUrl;
    public function __construct($userId, $title, $message, $imageUrl = null)
    {
        // Validate input
        if (empty($userId) || empty($title) || empty($message)) {
            throw new \InvalidArgumentException('Invalid notification data');
        }
    
        $this->userId = $userId;
        $this->title = $title;
        $this->message = $message;
        $this->imageUrl = $imageUrl; // Set URL gambar

        // $this->createNotification();
    }
    // private function createNotification()
    // {
    //     Notification::create([
    //         'user_id' => $this->userId,
    //         'title' => $this->title,
    //         'message' => $this->message,
    //         'image_url' => $this->imageUrl, // Simpan URL gambar di database
    //         'read' => false, // Add a read status
    //     ]);
    // }
    public function broadcastOn()
    {
        return new PrivateChannel('notifications.' . $this->userId);

    }
    

    public function broadcastAs()
    {
        return 'new.notification'; // Nama event di WebSocket
    }
    public function broadcastWith()
    {
        info('📢 Menyiarkan notifikasi', [
            'title' => $this->title,
            'message' => $this->message,
            'image_url' => $this->imageUrl,
        ]);
    
        return [
            'title' => $this->title,
            'message' => $this->message,
            'image_url' => $this->imageUrl,
        ];
    }

}
