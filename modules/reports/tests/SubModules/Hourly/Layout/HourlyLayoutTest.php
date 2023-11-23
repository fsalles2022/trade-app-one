<?php

namespace Reports\Tests\SubModules\Hourly\Layout;

use Reports\Exceptions\FailedReportBuildException;
use Reports\Tests\Fixture\HourlyLayoutFixture;
use Reports\SubModules\Hourly\Constants\HourConstants;
use Reports\SubModules\Hourly\Layout\HourlyLayout;
use TradeAppOne\Tests\TestCase;

class HourlyLayoutTest extends TestCase
{
    /** @test */
    public function should_return_an_instance()
    {
        $data      = HourlyLayoutFixture::sale();
        $class     = new HourlyLayout($data);
        $className = get_class($class);
        $this->assertEquals(HourlyLayout::class, $className);
    }

    /** @test */
    public function should_to_html_return_string()
    {
        $data  = HourlyLayoutFixture::sale();
        $class = new HourlyLayout($data);

        $result = $class->toHtml();

        $this->assertInternalType('string', $result);
    }

    /** @test */
    public function should_invalid_structure_return_exception()
    {
        $this->expectException(FailedReportBuildException::class);
        new HourlyLayout([]);
    }

    /** @test */
    public function should_to_html_return_html()
    {
        $data  = HourlyLayoutFixture::sale();
        $class = new HourlyLayout($data);

        $result = $class->toHtml();

        $this->assertTrue($this->isHTML($result));
    }

    private function isHTML($text)
    {
        $processed = htmlentities($text);
        if ($processed == $text) {
            return false;
        }
        return true;
    }

    /** @test */
    public function should_exclude_headers()
    {
        $data  = HourlyLayoutFixture::sale();
        $class = new HourlyLayout($data, ['exclude' => [HourConstants::VALUES]]);

        $result = $class->getAvailableColumns($class->headersRows);

        self::assertContains(HourConstants::TOTAL, $result['RESUME']);
        self::assertNotContains(HourConstants::VALUES, $result['RESUME']);
    }

    /** @test */
    public function should_not_exclude_when_exclude_headers_options_is_not_array()
    {
        $data  = HourlyLayoutFixture::sale();
        $class = new HourlyLayout($data, ['exclude' => HourConstants::VALUES]);

        $result = $class->getAvailableColumns($class->headersRows);

        self::assertContains(HourConstants::TOTAL, $result['RESUME']);
        self::assertContains(HourConstants::VALUES, $result['RESUME']);
    }
}
