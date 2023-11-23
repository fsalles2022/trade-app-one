<?php

namespace Reports\Tests\Feature;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Mockery;
use Reports\Tests\Helpers\ElasticSearchHelper;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;
use TradeAppOne\Tests\Unit\Domain\Exportable\Mock\SalesMock;

class AnalyticalReportFeatureTest extends TestCase
{
    use AuthHelper, ElasticSearchHelper;

    protected $endpoint = '/reports/analytical_report';

    private $filters = [
        'startDate' => '2021-04-01T00:00:00-03:00',
        'endDate'   => '2021-04-15T23:55:00-03:00',
        'status'    => [
            'APPROVED'
        ]
    ];

    public function setUp()
    {
        parent::setUp();

        $this->mockSaleService();
    }

    /** @test */
    public function get_should_response_with_status_200()
    {
        $userHelper = (new UserBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('POST', '/' . $this->endpoint, $this->filters);

        $response->assertStatus(Response::HTTP_OK);
    }

    private function mockSaleService(): void
    {
        $this->instance(
            SaleService::class,
            Mockery::mock(SaleService::class, function ($mock): void {
                $mock->shouldReceive('filterAllActivationByContext')
                    ->twice()
                    ->andReturn(
                        $this->getSales(),
                        (new Sale())->newCollection()
                    );
            })
        );
    }

    private function getSales(): Collection
    {
        $sales = SalesMock::get();

        return with(new Sale())->newCollection($sales);
    }
}
