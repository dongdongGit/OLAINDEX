<?php

namespace App\Events\OneDrive;

use App\Events\Event;
use Illuminate\Broadcasting\PrivateChannel;

class UploadEvent extends Event
{
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        // TODO:
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
