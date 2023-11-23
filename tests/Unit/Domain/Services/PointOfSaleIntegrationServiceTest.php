<?php

namespace TradeAppOne\Tests\Unit\Domain\Services;

use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\Service;
use TradeAppOne\Domain\Models\Tables\ServiceOption;
use TradeAppOne\Domain\Repositories\Collections\PointOfSaleRepository;
use TradeAppOne\Domain\Services\PointOfSaleIntegrationService;
use TradeAppOne\Exceptions\SystemExceptions\PointOfSaleExceptions;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\TestCase;

class PointOfSaleIntegrationServiceTest extends TestCase
{
    /** @test */
    public function should_return_exception_when_pos_not_found(): void
    {
        $this->expectExceptionCode(PointOfSaleExceptions::NOT_FOUND);

        $this->service()->updateSivIntegration([
            'codigo' => 'FAKE'
        ]);
    }

    /** @test */
    public function should_attach_chip_combo_to_the_pdv(): void
    {
        $service = Service::query()->create([
            'sector' => Operations::LINE_ACTIVATION,
            'operator' => Operations::CLARO,
            'operation' => Operations::CLARO_PRE
        ]);

        $pos = PointOfSaleBuilder::make()
            ->withServices($service)
            ->build();

        $pos->providerIdentifiers = '{"CLARO": "CUST_CODE"}';
        $pos->save();

        $mock = \Mockery::mock(PointOfSaleRepository::class)->makePartial();
        $mock->shouldReceive('findByProviderIdentifiers')->andReturn($pos);
        $service = new PointOfSaleIntegrationService($mock);

        $this->assertAttachChipCombo($pos, $service);
        $this->assertDetachChipCombo($pos, $service);
    }

    private function assertAttachChipCombo(PointOfSale $pos, $service): void
    {
        $service->updateSivIntegration([
            'codigo'     => 'CUST_CODE',
            'chip_combo' => '1'
        ]);

        $options = $pos->availableServicesRelation()
            ->with('options')
            ->get()
            ->pluck('options')
            ->collapse();

        $this->assertTrue($options->contains('action', '=', ServiceOption::CLARO_PRE_CHIP_COMBO));
        $this->assertCount(1, $options);
    }

    private function assertDetachChipCombo(PointOfSale $pos, $service): void
    {
        $service->updateSivIntegration([
            'codigo'     => 'CUST_CODE',
            'chip_combo' => '0'
        ]);

        $options = $pos->availableServicesRelation()
            ->with('options')
            ->get()
            ->pluck('options')
            ->collapse();

        $this->assertFalse($options->contains('action', '=', ServiceOption::CLARO_PRE_CHIP_COMBO));
        $this->assertTrue($options->isEmpty());
    }

    /** @test */
    public function should_attach_cf_lio_to_the_pdv(): void
    {
        $service = Service::query()->create([
            'sector' => Operations::LINE_ACTIVATION,
            'operator' => Operations::CLARO,
            'operation' => Operations::CLARO_CONTROLE_FACIL
        ]);

        $pos = PointOfSaleBuilder::make()
            ->withServices($service)
            ->build();

        $pos->providerIdentifiers = '{"CLARO": "CUST_CODE"}';
        $pos->save();

        $mock = \Mockery::mock(PointOfSaleRepository::class)->makePartial();
        $mock->shouldReceive('findByProviderIdentifiers')->andReturn($pos);
        $service = new PointOfSaleIntegrationService($mock);

        $this->assertAttachCfLio($pos, $service);
        $this->assertDetachCfLio($pos, $service);
    }

    private function assertAttachCfLio(PointOfSale $pos, $service): void
    {
        $service->updateSivIntegration([
            'codigo' => 'CUST_CODE',
            'cf_lio' => '1'
        ]);

        $options = $pos->availableServicesRelation()
            ->with('options')
            ->get()
            ->pluck('options')
            ->collapse();

        $this->assertTrue($options->contains('action', '=', ServiceOption::CONTROLE_FACIL_LIO));
        $this->assertCount(1, $options);
    }

    private function assertDetachCfLio(PointOfSale $pos, $service): void
    {
        $service->updateSivIntegration([
            'codigo' => 'CUST_CODE',
            'cf_lio' => '0'
        ]);

        $options = $pos->availableServicesRelation()
            ->with('options')
            ->get()
            ->pluck('options')
            ->collapse();

        $this->assertFalse($options->contains('action', '=', ServiceOption::CONTROLE_FACIL_LIO));
        $this->assertTrue($options->isEmpty());
    }

    ########## Controle Facil LIO ############
    /** @test */
    public function should_attach_autentica_promoter_to_the_pdv(): void
    {
        $service = Service::query()->create([
            'sector' => Operations::LINE_ACTIVATION,
            'operator' => Operations::CLARO,
            'operation' => Operations::CLARO_CONTROLE_BOLETO
        ]);

        $pos = PointOfSaleBuilder::make()
            ->withServices($service)
            ->build();

        $pos->providerIdentifiers = '{"CLARO": "CUST_CODE"}';
        $pos->save();

        $mock = \Mockery::mock(PointOfSaleRepository::class)->makePartial();
        $mock->shouldReceive('findByProviderIdentifiers')->andReturn($pos);
        $service = new PointOfSaleIntegrationService($mock);

        $this->assertAttachAutenticaPromoter($pos, $service);
        $this->assertDetachAutenticaPromoter($pos, $service);
    }

    private function assertAttachAutenticaPromoter(PointOfSale $pos, $service): void
    {
        $service->updateSivIntegration([
            'codigo' => 'CUST_CODE',
            'claro_autentica_promotor' => '1'
        ]);

        $options = $pos->availableServicesRelation()
            ->with('options')
            ->get()
            ->pluck('options')
            ->collapse();

        $this->assertTrue($options->contains('action', '=', ServiceOption::AUTENTICA));
        $this->assertCount(1, $options);
    }

    private function assertDetachAutenticaPromoter(PointOfSale $pos, $service): void
    {
        $service->updateSivIntegration([
            'codigo' => 'CUST_CODE',
            'claro_autentica_promotor' => '0'
        ]);

        $options = $pos->availableServicesRelation()
            ->with('options')
            ->get()
            ->pluck('options')
            ->collapse();

        $this->assertFalse($options->contains('action', '=', ServiceOption::AUTENTICA));
        $this->assertTrue($options->isEmpty());
    }

    private function service(): PointOfSaleIntegrationService
    {
        return resolve(PointOfSaleIntegrationService::class);
    }
}
