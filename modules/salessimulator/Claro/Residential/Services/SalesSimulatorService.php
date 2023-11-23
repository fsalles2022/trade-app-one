<?php

declare(strict_types=1);

namespace SalesSimulator\Claro\Residential\Services;

use ClaroBR\Connection\SivConnectionInterface;
use SalesSimulator\Claro\Residential\Collections\PlansCollection;
use SalesSimulator\Claro\Residential\Entities\Address;

class SalesSimulatorService
{
    /** @var SalesSimulatorViabilityService */
    private $saleSimulatorViabilityService;

    /** @var SivConnectionInterface */
    private $sivConnection;

    public function __construct(
        SalesSimulatorViabilityService $salesSimulatorViabilityService,
        SivConnectionInterface $sivConnection
    ) {
        $this->saleSimulatorViabilityService = $salesSimulatorViabilityService;
        $this->sivConnection                 = $sivConnection;
    }

    /**
     * @param mixed[] $attributes
     * @return mixed[]
     */
    public function getPlansAndPromotions(array $attributes): array
    {
        $address = $this->saleSimulatorViabilityService->getViability($attributes);

        $response = $this->sivConnection->getResidentialPlansByCity(
            $address->getCityId(),
            $address->getCityId(),
            $this->adaptAttribute($address)
        );

        return (new PlansCollection($response->toArray()))->getPlansCollection();
    }

    private function adaptAttribute(Address $address): int
    {
        return $address->withViability() === true ? 0 : 1;
    }
}
