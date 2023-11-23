<?php

namespace TradeAppOne\Tests\Unit\Domain\Exportable;

use Reports\AnalyticalsReports\Input\SaleInlineInput;
use Reports\AnalyticalsReports\Input\SalesCollectionInlineInput;
use TradeAppOne\Domain\Exportables\AnalyticalReportExport;
use TradeAppOne\Tests\TestCase;
use TradeAppOne\Tests\Unit\Domain\Exportable\Mock\SalesMock;

class AnalyticalReportExportTest extends TestCase
{
    const CHANNEL = 'Varejo';

    /** @test */
    public function should_return_instance_of_analytical_report_export()
    {
        $customerService = new AnalyticalReportExport();
        $className       = get_class($customerService);
        $this->assertEquals(AnalyticalReportExport::class, $className);
    }

    /** @test */
    public function should_collection_return_collection_exported()
    {
        $customerService = AnalyticalReportExport::recordsToArray($this->getSales());
        self::assertEquals(count($customerService[0]), count($customerService[1]));
    }

    public function getSales(): SalesCollectionInlineInput
    {
        $sale = SalesMock::get()[0];

        return new SalesCollectionInlineInput([
            new SaleInlineInput($sale)
        ]);
    }

    /** @test */
    public function should_map_channel()
    {
        $customerService = AnalyticalReportExport::recordsToArray($this->getSales());
        self::assertArrayHasKey('Canal', array_flip($customerService[0]));
    }

    /** @return array */
    private function getExportedRow(): array
    {
        $expected = [
            'Canal'           => 'VAREJO',
            'Serviço'         => 'TELEFONIA',
            'Operadora'       => 'CLARO',
            'Origem'          => 'WEB',
            'Serviço ID'      => '2018102716342665-0_DEP-0',
            'Regional'        => 'Regional 2',
            'Data Venda'      => '27/10/2018',
            'Hora Venda'      => '16:26',
            'DDD'             => '31',
            'Tipo Serviço'    => 'PORTABILIDADE',
            'Plano'           => 'Claro Pós 10GB',
            'Plano Tipo'      => 'CLARO_POS',
            'Tipo Fatura'     => 'DEBITO_AUTOMATICO',
            'Status Venda'    => 'ATIVADO',
            'Valor Servico'   => 149.99,
            'MSISDN'          => '+5531999999999',
            'MSISDN Portado'  => '31999999999',
            'ICCID'           => '89550534310006433466',
            'IMEI'            => '356134091815768',
            'Modelo Aparelho' => 'iphone_6s_32gb',
            'Preço Aparelho'  => 1899,
            'Nome Vendedor'   => 'JOAO JOAO MORENO',
            'Cpf Vendedor'    => '0000000001',
            'Nome Cliente'    => 'DAS NEVES DAS NEVES',
            'CPF Cliente'     => '0000000000',
            'Cidade Cliente'  => 'Ouro Branco',
            'UF Cliente'      => 'MG',
            'Nome Pdv'        => 'Iplace - 609',
            'CNPJ Pdv'        => '00000000000000',
            'Cidade Pdv'      => 'BELO HORIZONTE',
            'UF Pdv'          => 'MG',
            'Nome Rede'       => 'Iplace',
            'Codigo da Rede'  => 'iplace',
            'Codigo do Pdv'   => '609'
        ];
        return $expected;
    }
}
