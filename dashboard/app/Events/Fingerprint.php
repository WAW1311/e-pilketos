<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Fingerprint implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $fp;
    /**
     * Create a new event instance.
     */
    public function __construct($fp)
    {
        $this->fp = $fp;
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('stored-fingerprint'),
        ];
    }

    public function broadcastAs()
    {
        return 'fingerprint.stored';
    }

    public function broadcastWith()
    {
        return [
            'fingerprint' => $this->fp,
        ];
    }
}
