<?php

namespace App\Listeners;

use App\Events\OneDrive\Upload\CompletedEvent;

class OneDriveEventSubscriber
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function handleUploadCompleted(CompletedEvent $event)
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
            'App\Events\OneDrive\Upload\CompletedEvent',
            'App\Listeners\OneDriveEventSubscriber@handleUploadCompleted'
        );
    }
}
