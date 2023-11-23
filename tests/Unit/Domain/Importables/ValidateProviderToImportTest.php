<?php

namespace TradeAppOne\Tests\Unit\Domain\Importables;

use NextelBR\Enumerators\NextelBRConstants;
use TradeAppOne\Domain\Enumerators\Channels;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Importables\ValidateProviderToImport;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Facades\Uniqid;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\TestCase;

class ValidateProviderToImportTest extends TestCase
{
    /** @test */
    public function should_create_custcode_when_has_not_pointOfSale_and_network_is_masterDealer_claro()
    {
        $line = [ValidateProviderToImport::CLARO => '1234'];
        $network = factory(Network::class)->create(['channel' => Channels::MASTER_DEALER]);

        $expected = json_encode([
            Operations::CLARO => $this->mockGenerateCustCode($network)
        ]);

        $provider = (new ValidateProviderToImport($network, $line))->make();
        $this->assertEquals($expected, $provider);
    }

    /** @test */
    public function should_return_same_custcode_when_has_not_pointOfSale_and_network_is_not_masterDealer_claro()
    {
        $line = [ValidateProviderToImport::CLARO => '1234'];

        $network = factory(Network::class)->create();
        $provider = (new ValidateProviderToImport($network, $line))->make();

        $expected = json_encode([Operations::CLARO => '1234']);

        $this->assertEquals($expected, $provider);
    }

    /** @test */
    public function should_return_same_custcode_of_pointOfSale_when_network_is_masterDealer_in_update_claro()
    {
        $line = [ValidateProviderToImport::CLARO => '1234'];

        $network = factory(Network::class)->create(['channel' => Channels::MASTER_DEALER]);
        $pointOfSale = factory(PointOfSale::class)->create([
            'providerIdentifiers' => json_encode([Operations::CLARO => '5678']),
            'networkId' => $network->id
        ]);

        $provider = (new ValidateProviderToImport($network, $line, $pointOfSale))->make();

        $expected = json_encode([Operations::CLARO => '5678']);

        $this->assertEquals($expected, $provider);
    }

    /** @test */
    public function should_return_new_custcode_when_pointOfSale_has_not_providers_and_network_is_masterDealer_in_update_claro()
    {
        $line = [ValidateProviderToImport::CLARO => '1234'];

        $network = factory(Network::class)->create(['channel' => Channels::MASTER_DEALER]);
        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $expected = json_encode([Operations::CLARO => $this->mockGenerateCustCode($network)]);

        $provider = (new ValidateProviderToImport($network, $line, $pointOfSale))->make();

        $this->assertEquals($expected, $provider);
    }

    /** @test */
    public function should_return_same_custcode_when_network_is_not_masterDealer_in_update_claro()
    {
        $line = [ValidateProviderToImport::CLARO => '1234'];

        $network = factory(Network::class)->create();
        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $provider = (new ValidateProviderToImport($network, $line, $pointOfSale))->make();
        $expected = json_encode([Operations::CLARO => '1234']);

        $this->assertEquals($expected, $provider);
    }

    /** @test */
    public function should_return_exception_when_custcode_invalid_claro()
    {
        $line = [ValidateProviderToImport::CLARO => '#$#$!#$@'];
        $network = factory(Network::class)->create();

        $this->expectExceptionMessage(trans('siv::exceptions.InvalidClaroCode.message'));
        (new ValidateProviderToImport($network, $line))->make();
    }

    /** @test */
    public function should_return_custcode_tim()
    {
        $line = [ValidateProviderToImport::TIM => 'MX30_MXM37I_RD3271_VV01'];

        $network = factory(Network::class)->create();
        $provider = (new ValidateProviderToImport($network, $line))->make();

        $expected = json_encode([Operations::TIM => 'MX30_MXM37I_RD3271_VV01']);

        $this->assertEquals($expected, $provider);
    }

    /** @test */
    public function should_return_exceprion_when_custcode_invalid_tim()
    {
        $line = [ValidateProviderToImport::TIM => '#$#$!#$@'];
        $network = factory(Network::class)->create();

        $this->expectExceptionMessage(trans('timBR::exceptions.InvalidTimCode.message'));
        (new ValidateProviderToImport($network, $line))->make();
    }

    /** @test */
    public function should_return_custcode_nextel()
    {
        $line = [
            ValidateProviderToImport::NEXTEL_COD => '123-COD',
            ValidateProviderToImport::NEXTEL_REF => '123-REF'
        ];

        $expected = json_encode([
            Operations::NEXTEL => [
                NextelBRConstants::POINT_OF_SALE_COD => '123-COD',
                NextelBRConstants::POINT_OF_SALE_REF => '123-REF'
            ]
        ]);

        $network = factory(Network::class)->create();
        $provider = (new ValidateProviderToImport($network, $line))->make();
        $this->assertEquals($expected, $provider);
    }

    /** @test */
    public function should_return_custcode_oi()
    {
        $line = [ValidateProviderToImport::OI => '12345'];

        $expected = json_encode([Operations::OI => '12345']);

        $network = factory(Network::class)->create();
        $provider = (new ValidateProviderToImport($network, $line))->make();
        $this->assertEquals($expected, $provider);
    }

    /** @test */
    public function should_null_when_pass_none()
    {
        $line = [
            ValidateProviderToImport::CLARO => '',
            ValidateProviderToImport::NEXTEL_REF => '',
            ValidateProviderToImport::NEXTEL_COD => '',
            ValidateProviderToImport::OI => '',
            ValidateProviderToImport::TIM => ''
        ];
        $network = factory(Network::class)->create();

        $provider = (new ValidateProviderToImport($network, $line))->make();
        $this->assertEquals(null, $provider);
    }

    private function mockGenerateCustCode(Network $network): string
    {
        Uniqid::shouldReceive('generate')->andReturn('5d6d91360149d');
        return strtoupper($network->slug . '-' . substr(Uniqid::generate(), 5, 4));
    }
}
