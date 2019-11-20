<?php

namespace App\Jobs;

use Exception;
use App\Models\Task;
use Illuminate\Support\Facades\Artisan;

class OneDriveUpload extends Job
{
    protected $task;
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 2;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 3600;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        app('redis')->funnel('task_gid_' . $this->task->gid)->limit(1)->then(function () {
            // Job logic...
            if ($this->task->status == 'pending') {
                $parameters = [
                    'local'          => $this->task->source,
                    'remote'         => $this->task->target,
                    '--one_drive_id' => $this->task->onedrive_id,
                ];

                if ($this->task->type == 'folder') {
                    $parameters = array_merge($parameters, [
                        '--folder' => true
                    ]);
                }

                Artisan::call('od:upload', $parameters);
                $this->task->status = 'completed';
                $this->task->save();
            }
        }, function () {
            // Could not obtain lock...

            return $this->release(60);
        });
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        if (app()->bound('sentry')) {
            app('sentry')->captureException($exception);
        }

        $this->task->status = 'failed';
        $this->task->save();
    }
}
