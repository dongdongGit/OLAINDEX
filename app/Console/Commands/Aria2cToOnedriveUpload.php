<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use Illuminate\Support\Arr;

class Aria2cToOnedriveUpload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aria2c:upload_to_onedrive
                                {--gid=}
                                {--path=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '将aria2c下载的文件上传到onedrive';

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
        $gid = $this->option('gid');
        $path = $this->option('path');
        if (empty($gid) || empty($path)) {
            info('上传缺少参数', [
                'gid'  => $gid,
                'path' => $path
            ]);
            return;
        }

        getDefaultOneDriveAccount();
        $pathinfo = pathinfo($path);

        if (is_file($path)) {
            $target = Arr::last(explode('/', Arr::get($pathinfo, 'dirname', '/upload'))) . '/' . Arr::get($pathinfo, 'basename');
        } else {
            $target = Arr::get($pathinfo, 'basename');
        }

        $data = [
            'gid'         => $gid,
            'type'        => is_file($path) ? 'file' : 'folder',
            'source'      => $path,
            'target'      => $target,
            'onedrive_id' => app('onedrive')->id,
        ];

        $result = explode('@@', $path);
        foreach ($result as $item) {
            $match = [];

            if (preg_match('/(odid|path)=([\S]+)/', $item, $match)) {
                if (count($match) < 3) {
                    continue;
                }

                $need_match = array_pop($match);

                if (!empty($need_match)) {
                    continue;
                }

                if ($match[1] == 'path') {
                    $data['target'] = str_replace('\\', '/', $need_match);
                } else {
                    $data['onedrive_id'] = $need_match;
                }
            }
        }

        Task::create($data);
    }
}
