<?php

namespace App\Console\Commands;

use App\Jobs\OneDriveUpload;
use App\Models\Task;
use Illuminate\Console\Command;

class ManualCompletionTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'manual:complete_task {task_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '手动尝试失败的任务';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $task = Task::where('status', '!=', 'completed')->findOrFail($this->argument('task_id'));
        $task->update([
            'status' => 'pending'
        ]);

        dispatch(new OneDriveUpload($task));
        $this->info('手动推入队列，正在重新执行。。。');
    }
}
