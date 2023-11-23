<?php

declare(strict_types=1);

namespace TradeAppOne\Domain\Components\File;

use Closure;
use InvalidArgumentException;
use League\Csv\Writer;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;

/**
 * Class to process big CSVs,
 * Process results in bulk.
 */
class CsvInBulk
{
    /** @var string[] */
    protected $header = [];

    /** @var Closure */
    protected $processBulkCallback = null;

    /** @var int */
    protected $skip = 0;

    /** @var int */
    protected $take = 100;

    /** @param string[] $header */
    public function setHeader(array $header): self
    {
        $this->header = $header;

        return $this;
    }

    public function setProcessBulkCallback(Closure $callback): self
    {
        $this->processBulkCallback = $callback;

        return $this;
    }

    public function setSkip(int $skip): self
    {
        $this->skip = $skip;

        return $this;
    }

    public function setTake(int $take): self
    {
        $this->take = $take;

        return $this;
    }

    public function build(): Writer
    {
        $this->validArguments();

        $skip     = $this->skip;
        $take     = $this->take;
        $tempFile = tmpfile();
        $results  = collect([]);

        fwrite($tempFile, CsvHelper::arrayToCsv([$this->header])->getContent());

        do {
            $results = collect($this->processBulkCallback->__invoke($skip, $take));

            fwrite($tempFile, CsvHelper::arrayToCsv($results->all())->getContent());

            $skip += $this->take;
        } while ($results->isNotEmpty());

        $csv = CsvHelper::newFromFile($tempFile);

        fclose($tempFile);

        return $csv;
    }

    /** @throws InvalidArgumentException */
    protected function validArguments(): void
    {
        if (is_callable($this->processBulkCallback) === false) {
            throw new InvalidArgumentException('Argumento para processamento é necessário!', 500);
        }
    }
}
