<?php


namespace TradeAppOne\Domain\Exportables;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;

class EvaluationExportable
{
    protected $evaluations;

    public function __construct(Collection $evaluations)
    {
        $this->evaluations = $evaluations;
    }

    public function export()
    {
        $lines = [];
        array_push($lines, $this->headings());
        foreach ($this->evaluations as $evaluation) {
            array_push($lines, $this->collection($evaluation));
        }
        return CsvHelper::arrayToCsv($lines);
    }

    public function headings()
    {
        return [
          'ID',
          'Aparelho',
          'Excelente',
          'Bom',
          'Regular',
          'Rede'
        ];
    }

    public function collection($evaluations)
    {
        return [
            data_get($evaluations, 'id', ''),
            data_get($evaluations, 'devicesNetwork.device.label', ''),
            data_get($evaluations, 'goodValue', ''),
            data_get($evaluations, 'averageValue', ''),
            data_get($evaluations, 'defectValue', ''),
            data_get($evaluations, 'devicesNetwork.network.label', ''),
        ];
    }
}
