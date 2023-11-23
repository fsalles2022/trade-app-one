<?php

namespace TradeAppOne\Domain\Importables;

use Illuminate\Http\UploadedFile;
use League\Csv\Writer;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;
use TradeAppOne\Domain\Services\ImportHistoryRegistry;
use TradeAppOne\Exceptions\BusinessExceptions\ColumnNotFoundException;
use TradeAppOne\Exceptions\BusinessExceptions\FileUploadedInvalidNotFoundException;

class ImportEngine
{
    private const EXTRA_COLUMNS = 'extra_columns';

    /*** @var ImportableInterface */
    private $importable;

    public function __construct(ImportableInterface $importable)
    {
        $this->importable = $importable;
    }

    public function process(?UploadedFile $file, ?int $headerLine = null)
    {
        $importHistory = (new ImportHistoryRegistry())
            ->type($this->importable->getType())
            ->savePendingFile($file);

        [$header, $lines] = $this->initFile($file, $headerLine);

        $this->validateFile($header);

        $lines = $this->extractAndAdaptLines($lines, $header);

        $errorFile = null;

        foreach ($lines as $line) {
            try {
                $this->importable->processLine($line);
            } catch (\Exception $exception) {
                $originalLine = $this->buildOriginalLine($header, $line);
                $errorFile    = $errorFile ?? [array_merge($header, ["Erro"])];
                $errorFile[]  = array_merge($originalLine, ["{$exception->getMessage()}"]);
            }
        }

        if (is_null($errorFile)) {
            $importHistory->success();
            return null;
        }

        $errorFileCsv = CsvHelper::arrayToCsv($errorFile);
        $importHistory->saveErrorFile($errorFileCsv);

        return $errorFileCsv;
    }

    private function initFile(UploadedFile $file, ?int $headerLine = null): array
    {
        $content = CsvHelper::fileToCsv($file);
        $header  = $headerLine === null ? array_shift($content) : ($content[$headerLine] ?? null);
        $lines   = $content;

        throw_if((is_null($header) || empty($lines)), new FileUploadedInvalidNotFoundException());

        return [$header, $lines];
    }

    private function validateFile($header): bool
    {
        $header = array_map('strtolower', $header);
        $header = array_map('trim', $header);
        foreach ($this->importable->getColumns() as $columnName) {
            if (! in_array(strtolower($columnName), $header)) {
                throw new ColumnNotFoundException($columnName);
            }
        }

        return true;
    }

    private function extractAndAdaptLines($lines, $header)
    {
        $data = [];

        foreach ($lines as $lineNumber => $line) {
            foreach ($header as $position => $name) {
                $columnName   = array_search($name, $this->importable->getColumns());
                $linePosition = $line[$position];
                if ($columnName) {
                    $isUTF8 = mb_detect_encoding($line[$position], 'UTF-8', true);
                    if (! $isUTF8) {
                        $linePosition = iconv('macintosh', 'UTF-8', $line[$position]);
                    }
                    $data[$lineNumber][$columnName] = trim($linePosition);
                } else {
                    $data[$lineNumber][self::EXTRA_COLUMNS][$name] = trim($linePosition);
                }
            }
        }

        return $data;
    }

    private function buildOriginalLine($header, $line)
    {
        $originalLine = [];

        foreach ($header as $position => $name) {
            $columnName              = array_search($name, $this->importable->getColumns());
            $originalLine[$position] = $line[$columnName] ?? $line[self::EXTRA_COLUMNS][$name];
        }

        return $originalLine;
    }
}
