<?php

namespace Reports\Tests\SubModules\Hourly;

use Illuminate\Support\Facades\Mail;
use Reports\Enum\NetworkEmails;
use Reports\Tests\Fixture\HourlyLayoutFixture;
use Reports\Tests\Helpers\BindInstance;
use Reports\SubModules\Hourly\HourlyReportJob;
use Reports\SubModules\Hourly\HourlyReportService;
use TradeAppOne\Domain\Components\Printer\PdfHelper;
use TradeAppOne\Domain\Components\Telegram\Telegram;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Tests\TestCase;

class HourlyReportJobTest extends TestCase
{
    use BindInstance;

    /** @test */
    public function should_return_an_instance()
    {
        $class     = new HourlyReportJob(['network' => 'TradeUp']);
        $className = get_class($class);

        $this->assertEquals(HourlyReportJob::class, $className);
    }

    /** @test */

    public function should_send_email_with_report()
    {
        $options = [
            'network' => NetworkEnum::CEA,
            'date'    => null,
            'chatId'  => 'telegram.cea',
            'mailTo'  => NetworkEmails::CEA,
            'mailCC'  => NetworkEmails::CEA_CC
        ];

        $data = HourlyLayoutFixture::sale();
        $this->bindInstance(HourlyReportService::class)->shouldReceive('get')->andReturn($data);
        $this->bindInstance(PdfHelper::class)->shouldReceive('fromHtmlToContent');
        $this->bindInstance(Telegram::class)->shouldReceive('sendDocument');

        Mail::fake();
        Mail::shouldReceive('send')->once();

        $reportJob = new HourlyReportJob($options);
        $reportJob->handle();
    }

    /** @test */
    public function should_send_telegram_chat_with_report()
    {
        $options = [
            'network' => NetworkEnum::CEA,
            'date'    => null,
            'chatId'  => 'telegram.cea',
            'mailTo'  => NetworkEmails::CEA,
            'mailCC'  => NetworkEmails::CEA_CC
        ];

        $data = HourlyLayoutFixture::sale();
        $this->bindInstance(HourlyReportService::class)->shouldReceive('get')->andReturn($data);
        $this->bindInstance(PdfHelper::class)->shouldReceive('fromHtmlToContent');
        $this->bindInstance(Telegram::class)->shouldReceive('sendDocument')->once();

        $reportJob = new HourlyReportJob($options);
        $reportJob->handle();
    }

    /** @test */
    public function should_generate_render_report_pdf()
    {
        $options = [
            'network' => NetworkEnum::CEA,
            'date'    => null,
            'chatId'  => 'telegram.cea',
            'mailTo'  => NetworkEmails::CEA,
            'mailCC'  => NetworkEmails::CEA_CC
        ];

        $data = HourlyLayoutFixture::sale();
        $this->bindInstance(HourlyReportService::class)->shouldReceive('get')->andReturn($data);
        $this->bindInstance(PdfHelper::class)->shouldReceive('fromHtmlToContent')->once();
        $this->bindInstance(Telegram::class)->shouldReceive('sendDocument');

        $reportJob = new HourlyReportJob($options);
        $reportJob->handle();
    }
}
