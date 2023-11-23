<?php

namespace Reports\Tests\Unit\Goals;

use Reports\Goals\Models\Goal;
use Reports\Goals\Models\GoalType;
use Reports\Goals\Repository\GoalRepository;
use Reports\Goals\Repository\GoalTypeRepository;
use Reports\Goals\Services\GoalService;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Repositories\Collections\PointOfSaleRepository;
use TradeAppOne\Domain\Services\HierarchyService;
use TradeAppOne\Exceptions\BusinessExceptions\ModelInvalidException;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\TestCase;

class GoalServiceTest extends TestCase
{
    /** @test */
    public function should_persist_when_goal_is_new()
    {
        $type        = factory(GoalType::class)->create();
        $network     = factory(Network::class)->create();
        $pointOfSale = factory(PointOfSale::class)->create(['networkId' => $network->id]);
        $hierarchy   = (new HierarchyBuilder())
            ->withPointOfSale($pointOfSale)
            ->build();

        $assert     = [
            'month'  => now()->month,
            'year'   => now()->year,
            'cnpj'   => $pointOfSale->cnpj,
            'goal'   => 123,
            'goalTypeId' => $type->id
        ];
        $goalSevice = resolve(GoalService::class);
        $result     = $goalSevice->persist($assert);
        self::assertInstanceOf(Goal::class, $result);
    }

    /** @test */
    public function should_get_error_when_goal_is_outdated()
    {
        $this->expectException(ModelInvalidException::class);
        $type        = factory(GoalType::class)->create();
        $network     = factory(Network::class)->create();
        $pointOfSale = factory(PointOfSale::class)->create(['networkId' => $network->id]);
        $hierarchy   = (new HierarchyBuilder())
            ->withPointOfSale($pointOfSale)
            ->build();

        $assert     = [
            'month'  => now()->month,
            'year'   => now()->subYear()->year,
            'cnpj'   => $pointOfSale->cnpj,
            'goal'   => 123,
            'goalTypeId'  => $type->id
        ];
        $goalSevice = resolve(GoalService::class);
        $result     = $goalSevice->persist($assert);
        self::assertInstanceOf(Goal::class, $result);
    }

    /** @test */
    public function should_persist_when_goal_exists()
    {
        $type        = factory(GoalType::class)->create();
        $network     = factory(Network::class)->create();
        $pointOfSale = factory(PointOfSale::class)->create(['networkId' => $network->id]);
        $hierarchy   = (new HierarchyBuilder())
            ->withPointOfSale($pointOfSale)
            ->build();

        $assert     = [
            'month'   => now()->month,
            'year'    => now()->year,
            'cnpj'    => $pointOfSale->cnpj,
            'goal'    => 123,
            'goalTypeId'  => $type->id
        ];
        $goalSevice = resolve(GoalService::class);
        $first      = $goalSevice->persist($assert);
        $tryAgain   = $goalSevice->persist($assert);
        self::assertEquals($first->id, $tryAgain->id);
    }


    /** @test */
    public function should_call_update_when_goal_exists()
    {
        $type        = factory(GoalType::class)->create();
        $network     = factory(Network::class)->create();
        $pointOfSale = factory(PointOfSale::class)->create(['networkId' => $network->id]);
        $hierarchy   = (new HierarchyBuilder())
            ->withPointOfSale($pointOfSale)
            ->build();

        $assert     = [
            'month'  => now()->month,
            'year'   => now()->year,
            'cnpj'   => $pointOfSale->cnpj,
            'goal'   => 123,
            'goalTypeId' => $type->id
        ];
        $goalSevice = resolve(GoalService::class);
        $result     = $goalSevice->persist($assert);

        $goalRepository        = \Mockery::mock(GoalRepository::class)->makePartial();
        $pointOfSaleRepository = \Mockery::mock(PointOfSaleRepository::class)->makePartial();
        $goalRepository->shouldReceive('update')->once()->andReturn(new Goal());
        $goalRepository->shouldReceive('create')->never();
        $pointOfSaleRepository->shouldReceive('findOneBy')->withAnyArgs()->andReturn($pointOfSale);

        $goalSevice = new GoalService(
            $goalRepository,
            $pointOfSaleRepository,
            resolve(HierarchyService::class),
            resolve(GoalTypeRepository::class)
        );
        $goalSevice->persist($assert);
    }

    /** @test */
    public function should_call_create_when_goal_exists()
    {
        $type        = factory(GoalType::class)->create();
        $network     = factory(Network::class)->create();
        $pointOfSale = factory(PointOfSale::class)->create(['networkId' => $network->id]);
        $hierarchy   = (new HierarchyBuilder())
            ->withPointOfSale($pointOfSale)
            ->build();

        $assert = [
            'month' => now()->month,
            'year'  => now()->year,
            'cnpj'  => $pointOfSale->cnpj,
            'goal'  => 123,
            'goalTypeId'  => $type->id
        ];

        $goalRepository        = \Mockery::mock(GoalRepository::class)->makePartial();
        $pointOfSaleRepository = \Mockery::mock(PointOfSaleRepository::class)->makePartial();
        $goalRepository->shouldReceive('update')->never();
        $goalRepository->shouldReceive('create')->once()->andReturn(new Goal());
        $pointOfSaleRepository->shouldReceive('findOneBy')->withAnyArgs()->andReturn($pointOfSale);

        $goalSevice = new GoalService(
            $goalRepository,
            $pointOfSaleRepository,
            resolve(HierarchyService::class),
            resolve(GoalTypeRepository::class)
        );

        $goalSevice->persist($assert);
    }
}
