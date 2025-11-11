<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $userId;
    public $sender;
    /**
     * Create a new event instance.
     */
    public function __construct($message, $userId, $sender)
    {
        $this->message = $message;
        $this->userId = $userId;
        $this->sender = $sender;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        if ($this->sender === 'user') {
            return [new PrivateChannel('support')];
        }
        if ($this->sender === 'admin' || $this->sender === 'agent') {
            return [new PrivateChannel('user.' . $this->userId)];
        }

        return [];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}
