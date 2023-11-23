<?php

declare(strict_types=1);

namespace SurfPernambucanas\Services;

use SurfPernambucanas\Assistances\SurfPernambucanasAssistanceFactory;
use TradeAppOne\Domain\Models\Collections\Service;

/** Represent assistance of services */
class SurfPernambucanasSaleAssistance
{
    /**
     * @param mixed[] $payload
     * @return mixed
     */
    public function integrateService(Service $service, array $payload = [])
    {
        $assistance = SurfPernambucanasAssistanceFactory::make($service->operation);

        return $assistance->integrateService($service, $payload);
    }
}
