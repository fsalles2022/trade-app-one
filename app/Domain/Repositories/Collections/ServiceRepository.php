<?php

namespace TradeAppOne\Domain\Repositories\Collections;

use TradeAppOne\Domain\Models\Collections\Service;

class ServiceRepository extends BaseRepository
{
    protected $model = Service::class;

    public function updateService(Service $instance, array $attributes = []): Service
    {
        $instance->forceFill($attributes);
        $instance->touch();
        $instance->save();

        return $instance;
    }
}
