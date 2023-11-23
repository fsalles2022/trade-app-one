<?php

namespace Reports\Tests\SubModules\Hourly\Commands;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Reports\Tests\Fixture\HourlyLayoutFixture;
use Reports\Tests\Helpers\BindInstance;
use Reports\SubModules\Hourly\HourlyReportService;
use TradeAppOne\Domain\Components\Printer\PdfHelper;
use TradeAppOne\Domain\Components\Telegram\Telegram;
use TradeAppOne\Tests\TestCase;

class HourlyReportCommandTest extends TestCase
{
    use BindInstance;

    /** @test */
    public function should_when_call_send_to_cea()
    {
        $data = HourlyLayoutFixture::sale();
        Mail::fake();
        $this->bindInstance(HourlyReportService::class)->shouldReceive('get')->andReturn($data);
        $this->bindInstance(PdfHelper::class)->shouldReceive('fromHtmlToContent');
        $this->bindInstance(Telegram::class)->shouldReceive('sendDocument');
        Artisan::call('hourly:cea');
    }

    /** @test */
    public function should_when_call_send_to_pernambucanas()
    {
        $data = HourlyLayoutFixture::sale();
        Mail::fake();
        $this->bindInstance(HourlyReportService::class)->shouldReceive('get')->andReturn($data);
        $this->bindInstance(PdfHelper::class)->shouldReceive('fromHtmlToContent');
        $this->bindInstance(Telegram::class)->shouldReceive('sendDocument');
        Artisan::call('hourly:pernambucanas');
    }

    /** @test */
    public function should_when_call_send_to_lebes()
    {
        $data = HourlyLayoutFixture::sale();
        $this->bindInstance(HourlyReportService::class)->shouldReceive('get')->andReturn($data);
        $this->bindInstance(PdfHelper::class)->shouldReceive('fromHtmlToContent');
        $this->bindInstance(Telegram::class)->shouldReceive('sendDocument');
        Artisan::call('hourly:lebes');
    }

    /** @test */
    public function should_when_call_send_to_taqi()
    {
        $data = HourlyLayoutFixture::sale();
        $this->bindInstance(HourlyReportService::class)->shouldReceive('get')->andReturn($data);
        $this->bindInstance(PdfHelper::class)->shouldReceive('fromHtmlToContent');
        $this->bindInstance(Telegram::class)->shouldReceive('sendDocument');
        Artisan::call('hourly:taqi');
    }

    /** @test */
    public function should_when_call_send_to_extra()
    {
        $data = HourlyLayoutFixture::sale();
        $this->bindInstance(HourlyReportService::class)->shouldReceive('get')->andReturn($data);
        $this->bindInstance(PdfHelper::class)->shouldReceive('fromHtmlToContent');
        $this->bindInstance(Telegram::class)->shouldReceive('sendDocument');
        Artisan::call('hourly:extra', ['--exclude'=> 'VALUES']);
    }

//    /** @test */
//    public function should_when_call_send_to_iplace()        <-- removed by customer request
//    {
//        $data = HourlyLayoutFixture::sale();
//        $this->bindInstance(HourlyReportService::class)->shouldReceive('get')->andReturn($data);
//        $this->bindInstance(PdfHelper::class)->shouldReceive('fromHtmlToContent');
//        $this->bindInstance(Telegram::class)->shouldReceive('sendDocument');
//        Artisan::call('hourly:iplace');
//    }

    /** @test */
    public function should_when_callable_with_specific_date()
    {
        $data = HourlyLayoutFixture::sale();
        $this->bindInstance(HourlyReportService::class)->shouldReceive('get')->andReturn($data);
        $this->bindInstance(PdfHelper::class)->shouldReceive('fromHtmlToContent');
        $this->bindInstance(Telegram::class)->shouldReceive('sendDocument');

        Artisan::call('hourly:cea', ['--date' => '2018-10-12-10-10']);
    }

    /** @test */
    public function should_when_call_send_to_fujioka()
    {
        $data = HourlyLayoutFixture::sale();
        $this->bindInstance(HourlyReportService::class)->shouldReceive('get')->andReturn($data);
        $this->bindInstance(PdfHelper::class)->shouldReceive('fromHtmlToContent');
        $this->bindInstance(Telegram::class)->shouldReceive('sendDocument');
        Artisan::call('hourly:fujioka');
    }

    /** @test */
    public function should_when_call_send_to_schumann()
    {
        $data = HourlyLayoutFixture::sale();
        $this->bindInstance(HourlyReportService::class)->shouldReceive('get')->andReturn($data);
        $this->bindInstance(PdfHelper::class)->shouldReceive('fromHtmlToContent');
        $this->bindInstance(Telegram::class)->shouldReceive('sendDocument');
        Artisan::call('hourly:schumann');
    }
}
