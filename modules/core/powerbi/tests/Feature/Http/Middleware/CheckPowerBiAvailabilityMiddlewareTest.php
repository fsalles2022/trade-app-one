<?php

declare(strict_types=1);

namespace Core\PowerBi\tests\Feature\Http\Middleware;

use Core\PowerBi\Connections\PowerBiConnection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Mockery\MockInterface;
use Symfony\Component\HttpFoundation\Response;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\TestCase;

class CheckPowerBiAvailabilityMiddlewareTest extends TestCase
{
    use AuthHelper;

    public function test_method_check_power_bi_dashboard_offline(): void
    {
        $this->mockPowerBiConnection();
        Carbon::setTestNow(Carbon::create(21, 11, 17, 23, 0, 0));
        Storage::fake('s3');

        $this->authAs();
        $response = $this->get('/pbi/dashboard/mcafee');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'offline' => true,
            'accessToken' => '',
            'embedUrl' => ''
        ]);
    }

    public function test_method_check_power_bi_dashboard_online(): void
    {
        $this->mockPowerBiConnection();
        Carbon::setTestNow(Carbon::create(21, 11, 17, 12, 0, 0));

        $this->authAs();
        $response = $this->get('/pbi/dashboard/mcafee');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonMissing([
            'offline'
        ]);
    }

    private function mockPowerBiConnection(): void
    {
        $mock = Mockery::mock(PowerBiConnection::class, function (MockInterface $mock): void {
            $mock->shouldReceive('getDashboard')
                ->andReturn([
                    'type'        => 'report',
                    'accessToken' => 'awdawd-123123213-wadawdwad-23213123-awdawdwa-213123123',
                    'embedUrl'    => 'https://test.powerbi.com/reportEmbed?reportId=b04928fd-475b-49dc-977f-5b92e9e8aaaa&groupId=1234567898767',
                    'id'          => 'b04928fd-475b-49dc-977f-5b92e9e8aaaa'
                ]);
        });

        $this->instance(PowerBiConnection::class, $mock);
    }
}
