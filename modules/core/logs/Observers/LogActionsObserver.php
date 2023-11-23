<?php

namespace Core\Logs\Observers;

use Core\Logs\Jobs\LogActionsJob;
use Core\Logs\Adapters\LogActionsAdapter;
use Illuminate\Support\Facades\App;
use TradeAppOne\Domain\Enumerators\Environments;
use TradeAppOne\Domain\Models\Tables\BaseModel;

class LogActionsObserver
{
    public function saving(BaseModel $model): void
    {
        if ($this->hasChanges($model) && $this->envIsNotTest()) {
            LogActionsJob::dispatch(LogActionsAdapter::get($model));
        }
    }

    public function hasChanges(BaseModel $model): bool
    {
        return filled($model->getDirty());
    }

    private function envIsNotTest(): bool
    {
        return App::environment() !== Environments::TEST;
    }
}
