<?php

namespace ClaroBR\Tests\Unit\Services;

use ClaroBR\Services\ViabilityService;
use ClaroBR\Tests\Siv3Tests\Siv3TestBook;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\TestCase;

class ClaroBrViabilityServiceTest extends TestCase
{
    public function test_should_correct_structure(): void
    {
        $service = factory(Service::class)->create([
            'customer' => [
                'firstName' => 'Name First',
                'lastName' => 'Last',
                'number' => 123,
                'cpf' => Siv3TestBook::SUCCESS_CPF_CREDIT,
                'complement' => 'complement',
                'zipCode' => str_replace('-', '', Siv3TestBook::SUCCESS_POSTAL_CODE),
                'birthday' => '1996-07-20',
            ]
        ]);

        $sale      = (new SaleBuilder())->withServices([$service])->build();
        $viability = resolve(ViabilityService::class)->getViability($sale->services->first()->serviceTransaction);

        $this->assertArrayHasKey('viability', $viability);
        $this->assertArrayHasKey('status', $viability['viability']);
        $this->assertTrue($viability['viability']['status']);
    }
}
