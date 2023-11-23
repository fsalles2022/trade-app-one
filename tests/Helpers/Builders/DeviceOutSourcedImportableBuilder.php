<?php

namespace TradeAppOne\Tests\Helpers\Builders;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;
use TradeAppOne\Domain\Importables\DeviceOutSourcedImportable;
use TradeAppOne\Domain\Models\Tables\Network;

class DeviceOutSourcedImportableBuilder
{
    private $network;

    public function __construct()
    {
        Storage::fake('local');
    }

    public function withNetwork(Network $network): DeviceOutSourcedImportableBuilder
    {
        $this->network = $network;
        return $this;
    }

    public function build()
    {
        $network = $this->network ?? (new NetworkBuilder())->build();

        $importable = new DeviceOutSourcedImportable();
        $columns = array_values($importable->getColumns());
        $lines   = $importable->getExample($network->slug);

        $csvFile = CsvHelper::arrayToCsv([$columns ,$lines]);
        $filePath = '/tmp/randomstring.csv';
        file_put_contents($filePath, $csvFile->getContent());

        return new UploadedFile($filePath, 'file.csv', 'text/csv', null, null, true);
    }
}
