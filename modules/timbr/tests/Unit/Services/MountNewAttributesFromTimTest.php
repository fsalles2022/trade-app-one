<?php

namespace TimBR\Tests\Unit\Services;

use Illuminate\Support\Facades\Cache;
use TimBR\Models\Eligibility;
use TimBR\Models\TimBRControleFatura;
use TimBR\Services\MountNewAttributesFromTim;
use TimBR\Tests\Helpers\TimFactoriesHelper;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class MountNewAttributesFromTimTest extends TestCase
{
    use TimFactoriesHelper;

    /** @test */
    public function should_return_without_loyalty()
    {
        $eligibility                   = new Eligibility();
        $eligibility->eligibilityToken = '123';
        $eligibility->products         = collect(
            json_decode(
                file_get_contents(__DIR__ . '/../../ServerTest/controleFaturaEligibilitySucess.json'),
                true
            )['products']
        );
        Cache::shouldReceive('get')->withAnyArgs()->andReturn($eligibility);
        $mountService = resolve(MountNewAttributesFromTim::class);
        $tim          = $this->timFactories()
            ->of(TimBRControleFatura::class)
            ->make();
        $network      = factory(Network::class)->create(['slug' => 'cea']);
        $user         = (new UserBuilder())->withNetwork($network)->build();
        $this->actingAs($user);
        $return = $mountService->getAttributes($tim->toArray());
        self::assertArrayHasKey('price', $return);
        self::assertArrayHasKey('eligibilityToken', $return);
    }

    /** @test */
    public function should_return_loyalty()
    {
        $eligibility                   = new Eligibility();
        $eligibility->eligibilityToken = '234';
        $eligibility->products         = collect(
            json_decode(
                file_get_contents(__DIR__ . '/../../ServerTest/controleFaturaEligibilitySucess.json'),
                true
            )['products']
        );
        Cache::shouldReceive('get')->withAnyArgs()->andReturn($eligibility);
        $mountService = resolve(MountNewAttributesFromTim::class);
        $tim          = $this->timFactories()
            ->of(TimBRControleFatura::class)
            ->states('loyalty')
            ->make();
        $network      = factory(Network::class)->create(['slug' => 'cea']);
        $user         = (new UserBuilder())->withNetwork($network)->build();
        $this->actingAs($user);
        $return = $mountService->getAttributes($tim->toArray());
        self::assertArrayHasKey('price', $return);
        self::assertArrayHasKey('eligibilityToken', $return);
        self::assertArrayHasKey('loyalty', $return);
        self::assertArrayHasKey('label', $return['loyalty']);
        self::assertEquals($return['loyalty']['price'], -15.0);
        self::assertEquals($return['price'], 64.99);
    }

    /** @test */
    public function should_return_service(): void
    {
        $eligibility                   = new Eligibility();
        $eligibility->eligibilityToken = '234';
        $eligibility->products         = collect(
            json_decode(
                file_get_contents(__DIR__ . '/../../ServerTest/controleFaturaEligibilitySucess.json'),
                true
            )['products']
        );
        Cache::shouldReceive('get')->withAnyArgs()->andReturn($eligibility);
        $mountService = resolve(MountNewAttributesFromTim::class);
        $tim          = $this->timFactories()
            ->of(TimBRControleFatura::class)
            ->states('withService')
            ->make();
        $network      = factory(Network::class)->create(['slug' => 'cea']);
        $user         = (new UserBuilder())->withNetwork($network)->build();
        $this->actingAs($user);
        $return = $mountService->getAttributes($tim->toArray());
        self::assertArrayHasKey('price', $return);
        self::assertArrayHasKey('eligibilityToken', $return);
        self::assertArrayHasKey('selectedServices', $return);
        self::assertArrayHasKey('label', $return['loyalty']);
        self::assertEquals($return['selectedServices'][0]['price'], 5);
        self::assertEquals($return['price'], 54.99);
    }

    /** @test */
    public function should_return_label(): void
    {
        $eligibility                   = new Eligibility();
        $eligibility->eligibilityToken = '234';
        $eligibility->products         = collect(
            json_decode(
                file_get_contents(__DIR__ . '/../../ServerTest/controleFaturaEligibilitySucess.json'),
                true
            )['products']
        );
        Cache::shouldReceive('get')->withAnyArgs()->andReturn($eligibility);
        $mountService = resolve(MountNewAttributesFromTim::class);
        $tim          = $this->timFactories()
            ->of(TimBRControleFatura::class)->make();
        $network      = factory(Network::class)->create(['slug' => 'cea']);
        $user         = (new UserBuilder())->withNetwork($network)->build();
        $this->actingAs($user);
        $return = $mountService->getAttributes($tim->toArray());
        self::assertArrayHasKey('label', $return);
        self::assertEquals($return['price'], 79.99);
    }
}
