<?php

declare(strict_types=1);

namespace SalesSimulator\Claro\Residential\Tests\Unit\Services;

use ClaroBR\Tests\Siv3Tests\Siv3TestBook;
use SalesSimulator\Claro\Residential\Entities\Plan;
use SalesSimulator\Claro\Residential\Entities\Promotion;
use SalesSimulator\Claro\Residential\Exceptions\SalesSimulatorResidentialException;
use SalesSimulator\Claro\Residential\Services\SalesSimulatorService;
use TradeAppOne\Exceptions\BuildExceptions;
use TradeAppOne\Tests\TestCase;

class SalesSimulatorServiceTest extends TestCase
{
    public function test_should_thrown_exception_not_found_address(): void
    {
        $salesSimulatorService = $this->getSalesSimulatorService();
        $this->expectException(BuildExceptions::class);
        $this->expectExceptionMessage(SalesSimulatorResidentialException::addressNotExists()->getMessage());
        $salesSimulatorService->getPlansAndPromotions(['zipCode' => '02524000']);
    }

    public function test_should_return_plans_and_promotions_when_has_viability(): void
    {
        $salesSimulatorService = $this->getSalesSimulatorService();
        $plansAndPromotions    = $salesSimulatorService->getPlansAndPromotions(['zipCode' => Siv3TestBook::SUCCESS_POSTAL_CODE]);

        $this->defaultTemplateWithAndWithoutPlansViability($plansAndPromotions);
    }

    public function test_should_return_plans_and_promotions_when_has_not_viability(): void
    {
        $salesSimulatorService              = $this->getSalesSimulatorService();
        $plansAndPromotionsWithoutViability = $salesSimulatorService->getPlansAndPromotions(['zipCode' => '07565250']);
        $this->defaultTemplateWithAndWithoutPlansViability($plansAndPromotionsWithoutViability);
    }

    private function getSalesSimulatorService(): SalesSimulatorService
    {
        /** @var SalesSimulatorService $salesSimulatorService */
        return resolve(SalesSimulatorService::class);
    }

    /** @param mixed[] $plans */
    private function defaultTemplateWithAndWithoutPlansViability(array $plans): void
    {
        /** @var Plan $plan */
        $plan = current($plans);

        /** @var Promotion $promotion */
        $promotion = current($plan->getPromotions());

        $this->assertInstanceOf(Plan::class, $plan);
        $this->assertInstanceOf(Promotion::class, $promotion);
        $this->assertNotEmpty($plan->getId());
        $this->assertNotEmpty($plan->getLabel());
        $this->assertNotEmpty($plan->getPrice());
        $this->assertNotEmpty($plan->getType());
        $this->assertNotEmpty($promotion->getLabel());
        $this->assertNotEmpty($promotion->getId());
    }
}
