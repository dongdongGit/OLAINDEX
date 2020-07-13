<?php

namespace App\Events\OneDrive\Upload;

use App\Events\Event;
use App\Models\Task;

class CompletedEvent extends Event
{
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }
}
