<?php

namespace App\Observers;

use App\Events\OneDrive\Upload\CompletedEvent;
use App\Jobs\OneDriveUpload;
use App\Models\Task;
use Illuminate\Support\Arr;

class TaskObserver
{
    public function created(Task $task)
    {
        dispatch(new OneDriveUpload($task));
    }

    /**
     * Handle the one drive "creating" event.
     *
     * @param  \App\Models\Task  $task
     * @return void
     */
    public function creating(Task $task)
    {
    }

    /**
     * Handle the task "saving" event.
     *
     * @param  \App\Models\Task  $task
     * @return void
     */
    public function saving(Task $task)
    {
        $newData = $task->getDirty();

        if (!empty($status = Arr::get($newData, 'status'))) {
            if (in_array($status . '_at', $task->getColumns())) {
                $task->setAttribute($status . '_at', now());
            }

            if ($status == 'completed') {
                event(new CompletedEvent($task));
                clearOnedriveCache($task->onedrive_id);
            }
        }
    }
}
