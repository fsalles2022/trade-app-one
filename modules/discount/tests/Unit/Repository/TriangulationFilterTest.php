<?php

namespace Discount\Tests\Unit\Repository;

use Discount\Enumerators\DiscountStatus;
use Discount\Models\DiscountProduct;
use Discount\Repositories\Filters\DiscountFilter;
use Discount\Tests\Helpers\Builders\DiscountBuilder;
use TradeAppOne\Domain\Models\Tables\DeviceOutSourced;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\TestCase;

class TriangulationFilterTest extends TestCase
{
    /** @test */
    public function should_filter_by_one_operator(): void
    {
        $discount  = (new DiscountBuilder())->build();
        $discount2 = (new DiscountBuilder())->build();

        factory(DiscountProduct::class)->create([
            'operator'   => 'OPERATOR-1',
            'discountId' => $discount->id
        ]);

        factory(DiscountProduct::class)->create([
            'operator'   => 'OPERATOR-2',
            'discountId' => $discount2->id
        ]);

        $filtered = (new DiscountFilter())
            ->operator('OPERATOR-1')
            ->get();

        $this->assertCount(1, $filtered);
        $this->assertEquals($discount->id, $filtered->first()->id);
    }

    /** @test */
    public function should_return_one_discount_by_intervalStrategy_when_date_within_period(): void
    {
        (new DiscountBuilder())
            ->startAt(now())
            ->endAt(now()->addDays(3)->endOfDay())
            ->build();

        $filtered = (new DiscountFilter())
            ->intervalStrategy(now()->addDay())
            ->get();

        $this->assertCount(1, $filtered);
    }

    /** @test */
    public function should_return_one_discount_by_intervalStrategy_when_date_before_period(): void
    {
        (new DiscountBuilder())
            ->startAt(now())
            ->endAt(now()->addDays(3)->endOfDay())
            ->build();

        $filtered = (new DiscountFilter())
            ->intervalStrategy(now()->subDay(2))
            ->get();

        $this->assertCount(1, $filtered);
    }

    /** @test */
    public function should_return_none_discount_by_intervalStrategy_when_date_after_period(): void
    {
        (new DiscountBuilder())
            ->startAt(now())
            ->endAt(now()->addDays(3)->endOfDay())
            ->build();

        $filtered = (new DiscountFilter())
            ->intervalStrategy(now()->addDay(5))
            ->get();

        $this->assertCount(0, $filtered);
    }

    /** @test */
    public function should_return_triangulation_filtered_by_startAt(): void
    {
        (new DiscountBuilder())
            ->startAt(now()->addDays(5))
            ->build();

        (new DiscountBuilder())
            ->startAt(now()->subDays(5))
            ->build();

        $filtered = (new DiscountFilter())
            ->startAt(now())
            ->get();

        $this->assertCount(1, $filtered);
    }

    /** @test */
    public function should_return_triangulation_filtered_by_available(): void
    {
        $discount = (new DiscountBuilder())
            ->startAt(now())
            ->endAt(now()->addDay())
            ->build();

        $filtered = (new DiscountFilter())
            ->available($discount->network->id)
            ->get();

        $this->assertCount(1, $filtered);
    }

    /** @test */
    public function should_return_none_triangulation_when_is_inactive_filtered_by_available(): void
    {
        $discount = (new DiscountBuilder())
            ->startAt(now())
            ->endAt(now()->addDay())
            ->withStatus(DiscountStatus::INACTIVE)
            ->build();

        $filtered = (new DiscountFilter())
            ->available($discount->network->id)
            ->get();

        $this->assertCount(0, $filtered);
    }

    /** @test */
    public function should_return_none_triangulation_when_not_belongs_to_same_network_filtered_by_available(): void
    {
        (new DiscountBuilder())
            ->startAt(now())
            ->endAt(now()->addDay())
            ->withStatus(DiscountStatus::ACTIVE)
            ->build();

        $network = (new NetworkBuilder())->build();

        $filtered = (new DiscountFilter())
            ->available($network->id)
            ->get();

        $this->assertCount(0, $filtered);
    }

    /** @test */
    public function should_return_none_triangulation_when_not_belongs_to_period_filtered_by_available(): void
    {
        $discount = (new DiscountBuilder())
            ->startAt(now()->addDays(2))
            ->endAt(now()->addDays(5))
            ->withStatus(DiscountStatus::ACTIVE)
            ->build();

        $filtered = (new DiscountFilter())
            ->available($discount->network->id)
            ->get();

        $this->assertCount(0, $filtered);
    }

    /** @test */
    public function should_return_one_triangulations_filtered_by_sku(): void
    {
        $network = (new NetworkBuilder())->build();
        $device  = factory(DeviceOutSourced::class)->create([
            'sku'       => '001',
            'networkId' => $network->id
        ]);

        $device2 = factory(DeviceOutSourced::class)->create([
            'sku'       => '002',
            'networkId' => $network->id
        ]);

        (new DiscountBuilder())
            ->withDevice($device)
            ->build();

        (new DiscountBuilder())
            ->withDevice($device2)
            ->build();

        $filtered = (new DiscountFilter())
            ->sku('001')
            ->get();

        $this->assertCount(1, $filtered);
        $this->assertEquals('001', $filtered->first()->devices->first()->device->sku);
    }

    /** @test */
    public function should_return_device_filtered_by_model(): void
    {
        $devices = factory(DeviceOutSourced::class, 2)->make();

        (new DiscountBuilder())->withDevice($devices[0])->build();
        (new DiscountBuilder())->withDevice($devices[1])->build();

        $filtered = (new DiscountFilter())->model($devices[0]->model)->getQuery()->limit(1)->get();

        $this->assertCount(1, $filtered);
        $this->assertEquals($devices[0]->model, $filtered->first()->devices->first()->device->model);
    }
}
