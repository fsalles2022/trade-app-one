<?php

namespace Core\PowerBi\tests\Feature;

use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\TestCase;

class PowerBiFeatureTest extends TestCase
{
    use AuthHelper;

    /** @test */
    public function should_return_config_dashboard(): void
    {
        $response = $this->authAs()->get('pbi/dashboard/mcafee');
        $response->assertJsonStructure(['type', 'accessToken', 'embedUrl', 'id']);
    }

    /** @test */
    public function should_return_config_dashboard_dashboardLads(): void
    {
        $response = $this->authAs()->get('pbi/dashboard/lads');
        $response->assertJsonStructure(['type', 'accessToken', 'embedUrl', 'id']);
    }

    /** @test */
    public function should_return_config_dashboard_dashboardSales(): void
    {
        $response = $this->authAs()->get('pbi/dashboard/management');
        $response->assertJsonStructure(['type', 'accessToken', 'embedUrl', 'id']);
    }

    /** @test */
    public function should_return_config_dashboard_insurance(): void
    {
        $response = $this->authAs()->get('pbi/dashboard/insurance');
        $response->assertJsonStructure(['type', 'accessToken', 'embedUrl', 'id']);
    }

    /** @test */
    public function should_return_config_dashboard_tradeIn(): void
    {
        $response = $this->authAs()->get('pbi/dashboard/tradeIn');
        $response->assertJsonStructure(['type', 'accessToken', 'embedUrl', 'id']);
    }

    /** @test */
    public function should_return_config_dashboard_telephony(): void
    {
        $this->authAs()
            ->get('pbi/dashboard/telephony')
            ->assertJsonStructure(['type', 'accessToken', 'embedUrl', 'id']);
    }
}
