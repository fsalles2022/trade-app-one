<?php


namespace TradeAppOne\Tests\Helpers\Builders;


use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use League\Csv\Writer;

class ServiceOptionsImportableBuilder
{
    private $fileName = 'file_name';

    /**
     * @param string $fileName
     * @return $this
     */
    public function withFileName(string $fileName): ServiceOptionsImportableBuilder
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function __construct()
    {
        Storage::fake('local');
    }

    public function buildFromArray(array $array): UploadedFile
    {
        $filePath = '/tmp/'. $this->fileName .'csv';
        $csv = Writer::createFromString("Codigo Pdv;Opcao;Valor\n");
        $csv->setDelimiter(";");
        $csv->insertAll($array);

        file_put_contents($filePath, $csv->getContent());

        return new UploadedFile($filePath, 'test.csv', 'application/csv', null, null, true);

    }

    public function buildInvalidFile(): UploadedFile
    {
        $filePath = '/tmp/test_file.csv';
        file_put_contents($filePath, "HeaderA,HeaderB,HeaderC\n");

        return new UploadedFile($filePath, 'test.csv', 'application/octet-stream', null, null, true);
    }
}
