<?php

declare(strict_types=1);

namespace SalesSimulator\Claro\Residential\Collections;

use SalesSimulator\Claro\Residential\Entities\Plan;
use SalesSimulator\Claro\Residential\Entities\Promotion;

class PlansCollection
{
    /** @var Plan[]|null */
    private $plans;

    /** @param mixed[] $plansAndPromotions */
    public function __construct(array $plansAndPromotions)
    {
        $this->setPlans($plansAndPromotions);
    }

    /** @param mixed[] $plansAndPromotions */
    private function setPlans(array $plansAndPromotions): void
    {
        foreach ($plansAndPromotions['data'] ?? [] as $planAndPromotion) {
            $this->plans[] = new Plan(
                $planAndPromotion['id'] ?? null,
                $planAndPromotion['label'] ?? null,
                $planAndPromotion['descricao'] ?? null,
                $planAndPromotion['residencial_plan_properties']['residencial_city_plan']['price'] ?? null,
                $planAndPromotion['plan_type']['nome'] ?? null,
                $this->setPromotions($planAndPromotion['residencial_plan_properties']['residencial_city_plan'] ?? null)
            );
        }
    }

    /**
     * @param mixed[]|null $promotionsResidentials
     * @return Promotion[]
     */
    private function setPromotions(?array $promotionsResidentials): array
    {
        $promotionCollection = [];

        foreach ($promotionsResidentials['residencial_cities_plans_promotions'] ?? [] as $promotions) {
            $promotionCollection[] = new Promotion(
                $promotions['promotion']['id'] ?? null,
                $promotions['promotion']['nome'] ?? null,
                $promotions['promotion']['valor'] ?? null
            );
        }

        return $promotionCollection;
    }

    /** @return Plan[] */
    public function getPlansCollection(): array
    {
        return $this->plans ?? [];
    }
}
