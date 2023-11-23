<?php

declare(strict_types=1);

namespace TimBR\Adapters;

class TimEligibilityPlansWithLoyaltyAdapter
{
    private $plans;
    private $filteredPlans;

    public function __construct()
    {
        $this->plans         = [];
        $this->filteredPlans = [];
    }

    /** @param mixed $plans */
    public function setPlans(array $plans): void
    {
        $this->plans = $plans;
    }

    /** @return mixed */
    public function getAdapted(): array
    {
        $this->filterPlansWithLoyalty();
        return $this->filteredPlans;
    }

    private function filterPlansWithLoyalty(): void
    {
        foreach ($this->plans as $plan) {
            if (data_get($plan, 'loyalty') !== null) {
                $this->filteredPlans[] = $plan;
            }
        }
    }
}
