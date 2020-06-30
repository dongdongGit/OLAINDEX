<?php

namespace App\Listeners;

use App\Events\Events\OneDrive\UploadEvent;

class OneDriveEventSubscriber
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        // TODO:
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            UploadEvent::class,
            'App\Listeners\OneDriveEventSubscriber@handleUserLogin'
        );
    }
}
