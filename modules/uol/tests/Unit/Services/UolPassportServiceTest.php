<?php

namespace Uol\Tests\Unit\Services;

use TradeAppOne\Exceptions\BuildExceptions;
use TradeAppOne\Tests\TestCase;
use Uol\Connection\Passaporte\UolPassaporteSoapClient;
use Uol\Services\UolPassaporteService;

class UolPassportServiceTest extends TestCase
{
    /** @test */
    public function should_return_passport_valid()
    {
        $passport = resolve(UolPassaporteService::class)->generate(50);
        $this->assertEquals(false, $passport->isNotConfirmed());
    }

    /** @test */
    public function should_return_passport_invalid_when_client_return_error()
    {
        $client = \Mockery::mock(UolPassaporteSoapClient::class)->makePartial();
        $client->shouldReceive('passportGenerated')
                ->andReturn([
                    'retorno' => 'false',
                    'numero' => 123,
                    'serie'  => 123
                ]);

        $service  = new UolPassaporteService($client);
        $passport = $service->generate(50);
        $this->assertEquals(true, $passport->isNotConfirmed());
    }

    /** @test */
    public function should_return_passport_invalid_when_error_in_confirmation()
    {
        $client = \Mockery::mock(UolPassaporteSoapClient::class)->makePartial();
        $client->shouldReceive('passportGenerated')
            ->andReturn([
                'retorno' => 'true',
                'numero' => 123,
                'serie'  => 123
            ]);

        $client->shouldReceive('confirmPassportGenerated')
            ->andReturn([
                'retorno' => 'false',
                'numero' => 123,
                'serie'  => 123
            ]);

        $service  = new UolPassaporteService($client);
        $passport = $service->generate(50);

        $this->assertEquals(true, $passport->isNotConfirmed());
    }

    /** @test */
    public function should_return_passport_invalid_when_error_in_cancel()
    {
        $service  = resolve(UolPassaporteService::class);
        $passport = $service->generate(50);

        $client = \Mockery::mock(UolPassaporteSoapClient::class)->makePartial();
        $client->shouldReceive('cancelPassport')
            ->andReturn([
                'retorno' => 'false',
                'serie'  => 123
            ]);

        $this->expectException(BuildExceptions::class);
        $service = new UolPassaporteService($client);
        $service->cancel($passport);
    }
}
