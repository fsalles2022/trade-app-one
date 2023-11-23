<?php

namespace TimBR\Tests\Unit\Connection;

use Illuminate\Support\Facades\Cache;
use TimBR\Connection\Headers\TimBRBaseHeader;
use TimBR\Enumerators\TimBRCacheables;
use TimBR\Exceptions\TimBRBearerSergeantFailed;
use TimBR\Tests\TimBRTestBook;
use TradeAppOne\Tests\TestCase;

class TimBRBaseHeaderTest extends TestCase
{
    /** @test */
    public function should_return_string_when_is_valid_token()
    {
        $base = new TimBRBaseHeader();

        $result = $base->extractSergeant(TimBRTestBook::getCeaBearer());

        self::assertNotEmpty($result);
    }

    /** @test */
    public function should_return_sergeant_when_is_valid_token()
    {
        $assertSergeant = 'T3506254';
        $base           = $this->getMockBuilder(TimBRBaseHeader::class)
            ->setMethods(['getNetwork'])->getMock();
        $base->method('getNetwork')->willReturn(TimBRTestBook::SUCCESS_USER_NETWORK);
        $this->mockCache();

        $result = $base->getSergeant(TimBRTestBook::SUCCESS_USER);

        self::assertEquals($assertSergeant, $result);
    }

    /** @test */
    public function should_throw_exception_when_token_is_invalid()
    {
        $base = new TimBRBaseHeader();

        $this->expectException(TimBRBearerSergeantFailed::class);
        $base->extractSergeant(TimBRTestBook::SUCCESS_USER);
    }

    protected function mockCache(): void
    {
        $key       = TimBRCacheables::USER_BEARER . TimBRTestBook::SUCCESS_USER_NETWORK . TimBRTestBook::SUCCESS_USER;
        $ceaBearer = TimBRTestBook::getCeaBearer();
        Cache::shouldReceive('get')->withArgs([$key])->andReturn($ceaBearer);
    }
}
