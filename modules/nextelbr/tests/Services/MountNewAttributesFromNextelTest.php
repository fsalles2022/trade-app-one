<?php

namespace NextelBR\Tests\Services;

use Illuminate\Support\Facades\Cache;
use NextelBR\Enumerators\NextelInvoiceTypes;
use NextelBR\Services\MountNewAttributesFromNextel;
use NextelBR\Tests\Helpers\NextelBRFactories;
use NextelBR\Models\NextelBRControleBoleto;
use NextelBR\Models\NextelBRControleCartao;
use TradeAppOne\Tests\TestCase;

class MountNewAttributesFromNextelTest extends TestCase
{
    use NextelBRFactories;

    /** @test */
    public function should_return_checking_account_dv_when_direct_debit_is_not_null()
    {
        $directDebit = ['checkingAccount' => 1234560];
        $assertDv    = substr($directDebit['checkingAccount'], -1);
        Cache::shouldReceive('get')->andReturn(['plans' => collect(), '']);
        $service      = new MountNewAttributesFromNextel();
        $nextelBoleto = $this->factory()->of(NextelBRControleBoleto::class)
            ->states('directDebit')->make(['directDebit' => $directDebit])->toArray();
        $result       = $service->getAttributes($nextelBoleto);
        self::assertEquals($assertDv, $result['directDebit']['checkingAccountDv']);
    }


    /** @test */
    public function should_return_invoice_type_debito_automatico_when_direct_debit_is_not_null()
    {
        $directDebit = ['checkingAccount' => 1234560];
        Cache::shouldReceive('get')->andReturn(['plans' => collect(), '']);
        $service      = new MountNewAttributesFromNextel();
        $nextelBoleto = $this->factory()->of(NextelBRControleBoleto::class)
            ->states('directDebit')->make(['directDebit' => $directDebit])->toArray();
        $result       = $service->getAttributes($nextelBoleto);
        self::assertEquals(NextelInvoiceTypes::DEBITO_AUTOMATICO_LIST, $result['invoiceType']);
    }

    /** @test */
    public function should_return_invoice_type_contole_cartao_when_direct_debit_is_not_null()
    {
        Cache::shouldReceive('get')->andReturn(['plans' => collect(), '']);
        $service      = new MountNewAttributesFromNextel();
        $nextelBoleto = $this->factory()->of(NextelBRControleCartao::class)->make()->toArray();
        $result       = $service->getAttributes($nextelBoleto);
        self::assertEquals(NextelInvoiceTypes::CARTAO_DE_CREDITO, $result['invoiceType']);
    }
}
