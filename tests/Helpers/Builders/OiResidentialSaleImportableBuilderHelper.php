<?php

declare(strict_types=1);

namespace TradeAppOne\Tests\Helpers\Builders;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;
use TradeAppOne\Domain\Importables\OiResidentialSaleImportable;

class OiResidentialSaleImportableBuilderHelper
{
    /** @var OiResidentialSaleImportable */
    private $oiResidentialSaleImportable;

    public function __construct(OiResidentialSaleImportable $oiResidentialSaleImportable)
    {
        $this->oiResidentialSaleImportable = $oiResidentialSaleImportable;
        Storage::fake('local');
    }

    public function buildFile(?string $salesmanCpf, ?string $pointOfSaleCnpj): UploadedFile
    {

        $example = $this->oiResidentialSaleImportable->getExample();

        $adapted = array_merge(
            $example,
            [
                'CPF Vendedor' => $salesmanCpf,
                'CNPJ PDV' => $pointOfSaleCnpj
            ]
        );

        $writer =CsvHelper::arrayToCsv([$this->oiResidentialSaleImportable->getColumns(), $adapted]);

        file_put_contents('/tmp/randomstring.csv', $writer->getContent());

        return new UploadedFile(
            '/tmp/randomstring.csv',
            'file.csv',
            'application/csv',
            null,
            null,
            true
        );
    }
}
