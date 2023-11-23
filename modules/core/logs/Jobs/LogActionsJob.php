<?php

namespace Core\Logs\Jobs;

use Core\Logs\Connection\LogActionsConnection;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Arr;

class LogActionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue;

    public $queue  = 'ACTIONS_LOG';
    protected $log = [];
    public $tries  = 3;

    public function __construct(array $log)
    {
        $this->log = $log;
    }

    public function handle(LogActionsConnection $connection)
    {
        $id = data_get($this->log, 'id');
        Arr::forget($this->log, 'id');
        $connection->save($this->log, $id);
    }
}
