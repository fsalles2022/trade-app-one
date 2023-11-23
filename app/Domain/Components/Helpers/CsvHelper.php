<?php

namespace TradeAppOne\Domain\Components\Helpers;

use League\Csv\AbstractCsv;
use Symfony\Component\HttpFoundation\StreamedResponse;
use function League\Csv\delimiter_detect;
use League\Csv\Reader;
use League\Csv\Writer;

class CsvHelper
{
    public static function arrayToCsv(array $rows): Writer
    {
        $csv = Writer::createFromString('');

        $csv->setDelimiter(';');

        $csv->insertAll($rows);

        return $csv;
    }

    public static function fileToCsv($file): array
    {
        $csv = Reader::createFromString(file_get_contents($file));

        $delimiter = self::findDelimiter($csv);

        $csv->setDelimiter($delimiter);

        $records = $csv->getRecords();

        $data = [];

        foreach ($records as $offset => $record) {
            $data[] = $record;
        }

        return $data;
    }

    public static function findDelimiter(
        AbstractCsv $csv,
        $delimiters = [',', '.', ';', '|', "\t", ':'],
        $numberOfLines = 2
    ) {
        $delimiterCount = delimiter_detect($csv, $delimiters, $numberOfLines);

        arsort($delimiterCount);

        return key($delimiterCount)[0];
    }

    public static function newFromString(): Writer
    {
        $csv = Writer::createFromString('');
        $csv->setDelimiter(';');

        return $csv;
    }

    public static function exportDataToCsvFile($data, $filename): StreamedResponse
    {
        $callback = static function () use ($data) {
            $file = fopen('php://output', 'wb');

            fwrite($file, (chr(0xEF) . chr(0xBB) . chr(0xBF)));

            foreach ($data as $fields) {
                fputcsv($file, $fields, ';', '"');
            }

            fclose($file);
        };

        return response()->streamDownload($callback, $filename . '.csv', [
            'Content-type'        => 'text/csv',
            'Content-disposition' => "attachment; filename=$filename.csv",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0'
        ]);
    }

    public static function newFromFile($file): Writer
    {
        $content = file_get_contents(stream_get_meta_data($file)['uri']);

        return Writer::createFromString($content);
    }
}
