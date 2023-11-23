<?php

namespace Buyback\Tests\Unit\Services;

use Buyback\Services\WaybillJob;
use TradeAppOne\Domain\Components\Printer\PdfHelper;
use TradeAppOne\Domain\Components\Telegram\Telegram;
use TradeAppOne\Tests\TestCase;

class WaybillJobTest extends TestCase
{
    /** @test */
    public function should_return_an_instance()
    {
        $class     = resolve(WaybillJob::class);
        $className = get_class($class);
        $this->assertEquals(WaybillJob::class, $className);
    }

    /** @test */
    public function should_not_return_exception_when_emails_valid()
    {
        $emails = ['trade@mail.com'];
        $this->service()->validateEmails($emails);
    }

    /** @test */
    public function should_return_exception_when_emails_invalid()
    {
        $emails = ['trade@mail.com', 'email-invalid'];

        $this->expectException(\InvalidArgumentException::class);
        $this->service()->validateEmails($emails);
    }

    private function service(): WaybillJob
    {
        return resolve(WaybillJob::class);
    }
}
