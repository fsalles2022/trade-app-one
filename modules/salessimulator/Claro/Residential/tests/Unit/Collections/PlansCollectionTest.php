<?php

declare(strict_types=1);

namespace SalesSimulator\Claro\Residential\Tests\Unit\Collection;

use SalesSimulator\Claro\Residential\Collections\PlansCollection;
use SalesSimulator\Claro\Residential\Entities\Plan;
use SalesSimulator\Claro\Residential\Entities\Promotion;
use TradeAppOne\Tests\TestCase;

class PlansCollectionTest extends TestCase
{
    public function test_should_return_an_collection_of_plans_with_viability(): void
    {
        $plansCollection = new PlansCollection($this->getPlansAndPromotionsWithViability());

        $this->assertInstanceOf(Plan::class, $plansCollection->getPlansCollection()[0]);
        $this->assertInstanceOf(Promotion::class, $plansCollection->getPlansCollection()[0]->getPromotions()[0]);
    }

    public function test_should_return_an_collection_of_plans_without_viability(): void
    {
        $plansCollection = new PlansCollection($this->getPlansAndPromotionsWithoutViability());

        $this->assertInstanceOf(Plan::class, $plansCollection->getPlansCollection()[0]);
        $this->assertInstanceOf(Promotion::class, $plansCollection->getPlansCollection()[0]->getPromotions()[0]);
    }

    /** @return mixed[] */
    private function getPlansAndPromotionsWithoutViability(): array
    {
        $file = file_get_contents(
            __DIR__ . '../../../../../../../clarobr/tests/ServerTest/Response/residentialPlans/PlansWithoutViability.json'
        );
        return json_decode($file, true);
    }

    /** @return mixed[] */
    private function getPlansAndPromotionsWithViability(): array
    {
        $file = file_get_contents(
            __DIR__ . '../../../../../../../clarobr/tests/ServerTest/Response/residentialPlans/PlansWithViability.json'
        );
        return json_decode($file, true);
    }
}
