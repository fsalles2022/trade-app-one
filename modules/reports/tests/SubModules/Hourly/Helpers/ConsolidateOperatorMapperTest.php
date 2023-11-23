<?php

namespace Reports\Tests\SubModules\Hourly\Helpers;

use Reports\SubModules\Hourly\Helpers\ConsolidateOperatorMapper;
use function GuzzleHttp\Psr7\stream_for;

class ConsolidateOperatorMapperTest extends \TradeAppOne\Tests\TestCase
{

    /** @test */
    public function should_return_an_instance()
    {
        $class = new ConsolidateOperatorMapper();

        $this->assertInstanceOf(ConsolidateOperatorMapper::class, $class);
    }

    /** @test */
    public function should_return_correct()
    {
        $elasticResult = $this->getMockedFile();

        ConsolidateOperatorMapper::mapValuesFromConsolidateOperations($elasticResult);
    }

    private function getMockedFile()
    {
        return json_decode(stream_for(file_get_contents(base_path('modules/reports/tests/Fixture/HourlyReport/result.json'))), true);
    }
}
