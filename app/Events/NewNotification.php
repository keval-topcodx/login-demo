<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;
    public $chatId;
    public $sender;
    public $user_type;
    public $userId;

    /**
     * Create a new event instance.
     */
    public function __construct($notification, $chatId, $sender, $user_type, $userId)
    {
        $this->notification = $notification;
        $this->chatId = $chatId;
        $this->sender = $sender;
        $this->user_type = $user_type;
        $this->userId = $userId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        if ($this->user_type === 'user') {
            return [new PrivateChannel('notification')];
        }
        if ($this->user_type === 'admin' || $this->user_type === 'agent') {
            return [new PrivateChannel('notification.' . $this->userId)];
        }
        return [];
//        return [
//            new PrivateChannel('notification'),
//        ];
    }

    public function broadcastAs(): string
    {
        return 'notification-received';
    }
}
