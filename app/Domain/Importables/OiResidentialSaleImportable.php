<?php

declare(strict_types=1);

namespace TradeAppOne\Domain\Importables;

use League\Csv\Writer;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;
use TradeAppOne\Domain\Components\Helpers\ImportableHelper;
use TradeAppOne\Domain\Enumerators\Importables;
use TradeAppOne\Domain\Services\OiResidentialSaleImportableService;

class OiResidentialSaleImportable implements ImportableInterface
{
    /** @var OiResidentialSaleImportableService */
    private $oiResidentialSaleImportableService;

    public function __construct(OiResidentialSaleImportableService $oiResidentialSaleImportableService)
    {
        $this->oiResidentialSaleImportableService = $oiResidentialSaleImportableService;
    }

    /** @return string[] */
    public function getColumns(): array
    {
        return [
            'salesmanCpf' => 'CPF Vendedor',
            'pdv' => 'Ponto de Venda',
            'cnpj' => 'CNPJ PDV',
            'plan' => 'Plano',
            'valuePlan' => 'Valor Plano',
            'customerName' => 'Nome Cliente',
            'customerCpf' => 'CPF Cliente',
            'customerAddress' => 'Endereço Cliente',
            'customerAddressNumber' => 'Numero Endereço Cliente',
            'customerAddressComplement' => 'Complemento Endereco Cliente',
            'customerCity' => 'Cidade Cliente',
            'customerState' => 'UF Cliente',
            'customerZipCode' => 'CEP Cliente',
            'customerBirthday' => 'Data Nascimento Cliente',
            'createdAt' => 'Data da Venda',
            'statusSale' => 'Status Venda'
        ];
    }

    /** @param mixed[] $line */
    public function processLine($line): void
    {
        ImportableHelper::hasErrorInLine(
            $line,
            array_merge(
                self::getRulesCustomer(),
                self::getRulesPlan(),
                self::getRulesPdv(),
                self::getSales(),
                self::getRuleSaleman()
            ),
            $this->getColumns()
        );

        $this->oiResidentialSaleImportableService->validateIfSaleExists($line);
        $this->oiResidentialSaleImportableService->adaptedData($line);
        $this->oiResidentialSaleImportableService->saveOiResidentialSale();
    }

    /** @return string[] */
    private static function getRulesCustomer(): array
    {
        return [
            'customerName' => 'required|string|max:60',
            'customerCpf' => 'required|cpf',
            'customerAddress' => 'required|string',
            'customerNumberAddress' => 'string',
            'customerAddressComplement' => 'string',
            'customerCity' => 'string',
            'customerState' => 'string',
            'customerZipCode' => 'required|string',
            'customerBirthday' => 'string'
        ];
    }

    /** @return string[] */
    private static function getRulesPlan(): array
    {
        return [
            'plan' => 'required|string',
            'valuePlan' => 'required|string'
        ];
    }

    /** @return string[] */
    private static function getRulesPdv(): array
    {
        return [
            'pdv' => 'required|string',
            'cnpj' => 'required|string'
        ];
    }

    /** @return string[] */
    private static function getRuleSaleman(): array
    {
        return [
            'salesmanCpf' => 'required|cpf'
        ];
    }

    /** @return string[] */
    private static function getSales(): array
    {
        return [
            'createdAt' => 'required|string',
            'statusSale' => 'required|string'
        ];
    }

    public function getType(): string
    {
        return Importables::OI_RESIDENTIAL_SALE;
    }

    /** @return string[] */
    public function getHeaders(): array
    {
        return array_values($this->getColumns());
    }

    /** @return string[] */
    public function getExample(): array
    {
        return [
            'CPF Vendedor' => '33655038895',
            'Ponto de Venda' => 'XPTO',
            'CNPJ PDV' => '94538584343043',
            'Plano' => 'Promocao TradeUpGroup',
            'Valor Plano' => '200',
            'Nome Cliente' => 'Emilly Gutter',
            'CPF Cliente' => '33655038895',
            'Endereco Cliente' => 'Rua Bonnard',
            'Numero Endereco Cliente' => '980',
            'Complemento Endereco Cliente' => '5 Andar',
            'Cidade Cliente' => 'Barueri',
            'UF Cliente' => 'SP',
            'CEP Cliente' => '06465-134',
            'Data Nascimento Cliente' => '02/02/1993',
            'Data da Venda' => '22/03/2022 12:58:34',
            'Status Venda' => 'APROVADO',
        ];
    }

    public static function buildExample(): Writer
    {
        $oiResidentialSaleImportable = resolve(__CLASS__);
        return CsvHelper::arrayToCsv(
            [
                $oiResidentialSaleImportable->getColumns(),
                $oiResidentialSaleImportable->getExample()
            ]
        );
    }
}
