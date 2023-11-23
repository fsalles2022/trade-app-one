<?php

namespace Core\WebHook\Observers;

use Illuminate\Support\Facades\App;
use TradeAppOne\Domain\Enumerators\Environments;
use TradeAppOne\Domain\Models\Collections\BaseModel;
use TradeAppOne\Domain\Models\Collections\Service;

class WebHookServiceObserver
{
    public function saved(Service $service): void
    {
        // TODO check if this it's necessary
//        if ($this->hasChanges($service) && ! $this->isEnvTest()) {
//            //WebHookJob::dispatch($service->serviceTransaction, $service->getDirty());
//        }
    }

    public function hasChanges(BaseModel $model): bool
    {
        return $model->isDirty();
    }

    private function isEnvTest(): bool
    {
        $excludeEnvs = [
            Environments::TEST,
            Environments::BETA
        ];

        return in_array(App::environment(), $excludeEnvs, true);
    }
}
