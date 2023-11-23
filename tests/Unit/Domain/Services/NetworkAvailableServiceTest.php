<?php


namespace TradeAppOne\Tests\Unit\Domain\Services;


use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Policies\NetworkPolicy;
use TradeAppOne\Tests\TestCase;

class NetworkAvailableServiceTest extends TestCase
{
    /** @test */
    public function should_return_true_when_payload_has_all_availableServices(): void
    {
        $servicePayload = $this->payloadAvailableServices();

        $services = resolve(NetworkPolicy::class)
            ->validatedServices(Operations::SECTORS, $servicePayload);

        $this->assertTrue($services);
    }

    /** @test */
    public function should_return_false_when_no_found_values_in_availableServices(): void
    {
        $servicePayload = $this->payloadAvailableServices();
        $servicePayload['LINE_ACTIVATION']['OI'][0] = 'OI_NOT_FOUND';

        $services = resolve(NetworkPolicy::class)
            ->validatedServices(Operations::SECTORS, $servicePayload);

        $this->assertFalse($services);
    }

    private function payloadAvailableServices(): array
    {
        return [
        'LINE_ACTIVATION' => [
            'OI' => [
                0 => 'OI_CONTROLE_CARTAO',
                1 => 'OI_CONTROLE_BOLETO'
            ],
            'TIM' => [
                0 => 'TIM_EXPRESS',
                1 => 'TIM_CONTROLE_FATURA',
            ],
            'VIVO' => [
                0 => 'CONTROLE_CARTAO',
                1 => 'CONTROLE'
            ],
            'CLARO' => [
                0 => "CONTROLE_BOLETO",
                1 => 'CONTROLE_FACIL',
                2 => 'CLARO_PRE',
                3 => 'CLARO_POS',
                4 => 'CLARO_BANDA_LARGA'
            ]
        ]
    ];
    }
}
