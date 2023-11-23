<?php

namespace Reports\Tests\Feature;

use Illuminate\Http\Response;
use Reports\Tests\Fixture\ElasticSearchTaoFixture;
use Reports\Tests\Helpers\ElasticSearchHelper;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\TestCase;

class SalesReportFeatureTest extends TestCase
{
    use ElasticSearchHelper, AuthHelper;

    const ROUTE = 'reports/sales';

    /** @test */
    public function post_return_sales_report_pagined_when_pass_page()
    {
        $this->mock();
        $response = $this->authAs()->post(self::ROUTE, ['page' => 1]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure(['current_page', 'data', 'total', 'from', 'per_page']);
    }

    /** @test */
    public function post_return_sales_report_when_not_pass_page()
    {
        $this->mock();
        $response = $this->authAs()->post(self::ROUTE);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(10);
    }

    private function mock()
    {
        $elasticFixture = ElasticSearchTaoFixture::getSaleArray();
        $this->mockElasticSearchConnection($elasticFixture);
        return $this;
    }
}
