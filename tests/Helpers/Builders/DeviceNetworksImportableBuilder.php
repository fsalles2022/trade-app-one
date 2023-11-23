<?php

namespace TradeAppOne\Tests\Helpers\Builders;

use Illuminate\Http\UploadedFile;
use League\Csv\Writer;
use TradeAppOne\Domain\Importables\DevicesNetworkImportable;

class DeviceNetworksImportableBuilder
{
    public function buildFromArray(array $array)
    {
        $filePath = '/tmp/randomstring.csv';

        $deviceNetworksHeaders = array_values(resolve(DevicesNetworkImportable::class)->getColumns());

        $header = implode(';', $deviceNetworksHeaders);
        $csv    = Writer::createFromString("$header\n");

        $csv->setDelimiter(";");
        $csv->insertAll($array);

        file_put_contents($filePath, $csv->getContent());

        return new UploadedFile($filePath, 'test.csv', 'application/csv', null, null, true);

    }

}