<?php

declare(strict_types=1);

namespace Reports\Tests\Unit\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use League\Csv\Writer;
use Mockery;
use Reports\Services\AnalyticalReportService;
use Reports\Tests\Helpers\ElasticSearchHelper;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Tests\Helpers\UserHelper;
use TradeAppOne\Tests\TestCase;
use TradeAppOne\Tests\Unit\Domain\Exportable\Mock\SalesMock;

class AnalyticalReportServiceTest extends TestCase
{
    use UserHelper, ElasticSearchHelper;

    private $filter = [
        'startDate' => '2021-04-01T00:00:00-03:00',
        'endDate'   => '2021-04-15T23:55:00-03:00',
        'status'    => [
            'APPROVED'
        ]
    ];

    /** @test */
    public function return_should_be_instance_of_binary_file_response_when_was_premium_retail_operation()
    {
        $this->mockData();

        $user = $this->userSalesman()['user'];
        Auth::shouldReceive('user')->twice()->andReturn($user);

        $analyticalReportService = resolve(AnalyticalReportService::class);
        $result                  = $analyticalReportService->extractAnalytical($this->filter);

        $csvContent = $result->getContent();
        $list       = explode("\n", $csvContent);

        $headings   = str_getcsv(
            array_shift($list),
            ";"
        );

        $rowData     = str_getcsv($list[0], ";");
        $rowData     = array_pad($rowData, count($headings), '');
        $resultArray = array_combine($headings, $rowData);

        $this->assertInstanceOf(Writer::class, $result);
        $this->assertNotNull($resultArray['Nome testemunha 1']);
        $this->assertEquals('João da Silva', $resultArray['Nome testemunha 1']);
    }

    /** @test */
    public function return_should_be_instance_of_binary_file_response()
    {
        $this->mockData();

        $user = $this->userSalesman()['user'];
        Auth::shouldReceive('user')->twice()->andReturn($user);

        $analyticalReportService = resolve(AnalyticalReportService::class);
        $result                  = $analyticalReportService->extractAnalytical($this->filter);

        $this->assertInstanceOf(Writer::class, $result);
    }

    /** @test */
    public function return_should_be_instance_of_binary_file_response_when_elastic_response_contains_empty_hits()
    {
        $this->mockData();

        $user = $this->userSalesman()['user'];
        Auth::shouldReceive('user')->twice()->andReturn($user);

        $analyticalReportService = resolve(AnalyticalReportService::class);
        $result                  = $analyticalReportService->extractAnalytical($this->filter);

        $this->assertInstanceOf(Writer::class, $result);
    }

    /** @test */
    public function return_should_be_instance_of_binary_file_response_when_elastic_response_contains_empty_source()
    {
        $this->mockData();

        $user = $this->userSalesman()['user'];
        Auth::shouldReceive('user')->twice()->andReturn($user);

        $analyticalReportService = resolve(AnalyticalReportService::class);
        $result                  = $analyticalReportService->extractAnalytical($this->filter);

        $this->assertInstanceOf(Writer::class, $result);
    }

    private function mockData(): void
    {
        $this->mockSaleService();
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

        return with(new Sale)->newCollection($sales);
    }
}
