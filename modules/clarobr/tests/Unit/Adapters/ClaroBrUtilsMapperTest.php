<?php

namespace ClaroBR\Tests\Unit\Adapters;

use ClaroBR\Adapters\ClaroBrUtilsMapper;
use ClaroBR\Tests\ServerTest\ClaroBRResponseBook;
use TradeAppOne\Tests\TestCase;

class ClaroBrUtilsMapperTest extends TestCase
{

    /** @test */
    public function should_filter_return_collection()
    {
        $result = ClaroBrUtilsMapper::map([]);

        $this->assertInternalType('array', $result);
    }

    /** @test */
    public function should_return_domains_base_structure()
    {
        $utilsresponse = $this->getUtilsStub();

        $result = ClaroBrUtilsMapper::map($utilsresponse);

        $this->assertArrayHasKey('local', $result);
        $this->assertArrayHasKey('dueDate', $result);
        $this->assertArrayHasKey('banks', $result);
    }

    private function getUtilsStub()
    {
        $utilsFile = file_get_contents(ClaroBRResponseBook::SUCCESS_UTILS);
        return json_decode($utilsFile, true);
    }

    /** @test */
    public function should_return_domains_with_local_correct()
    {
        $utilsresponse = $this->getUtilsStub();

        $result = ClaroBrUtilsMapper::map($utilsresponse);

        $this->assertArrayHasKey('id', $result['local'][0]);
        $this->assertArrayHasKey('label', $result['local'][0]);
    }

    /** @test */
    public function should_return_domains_with_due_date_correct()
    {
        $utilsresponse = $this->getUtilsStub();

        $result = ClaroBrUtilsMapper::map($utilsresponse);

        $this->assertArrayHasKey('id', $result['dueDate'][0]);
        $this->assertArrayHasKey('dueDay', $result['dueDate'][0]);
        $this->assertArrayHasKey('closingDay', $result['dueDate'][0]);
    }

    /** @test */
    public function should_domains_return_with_banks()
    {
        $utilsresponse = $this->getUtilsStub();

        $result = ClaroBrUtilsMapper::map($utilsresponse);

        $this->assertArrayHasKey('id', $result['banks'][0]);
        $this->assertArrayHasKey('label', $result['banks'][0]);
    }

    /** @test */
    public function should_map_of_invalid_local_return_empty()
    {
        $response = [
            "logradouros" => [
                "id" => null,
                "nome" => null,
                "codigo_operadora" => null,
                "created_at" => null,
                "updated_at" => null,
            ],
        ];

        $result = ClaroBrUtilsMapper::map($response);

        $this->assertEmpty($result['local']);
    }

    /** @test */
    public function should_map_of_invalid_due_date_return_empty()
    {
        $response = [
            "logradouros" => [
                "id" => null,
                "codigo_operadora" => null,
                "vencimento" => null,
                "fechamento" => null,
                "created_at" => null,
                "updated_at" => null,
                "ativo" => null
            ],
        ];

        $result = ClaroBrUtilsMapper::map($response);

        $this->assertEmpty($result['dueDate']);
    }

    /** @test */
    public function should_map_of_invalid_banks_return_empty()
    {
        $response = [
            "bancos" => [
                "id" => null,
                "nome" => null,
                "numero" => null,
                "created_at" => null,
                "updated_at" => null,
                "ativo" => null
            ],
        ];

        $result = ClaroBrUtilsMapper::map($response);

        $this->assertEmpty($result['banks']);
    }
}
