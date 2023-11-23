<?php

namespace Reports\Goals\Exportables;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;

class GoalsExport
{
    public function exportToCsv(Collection $goals, array $accomplished)
    {
        $goalsAccomplished = $this->collection($goals, $accomplished);

        $lines = [];
        array_push($lines, $this->headings());

        foreach ($goalsAccomplished as $goal) {
            array_push($lines, $goal);
        }
        return CsvHelper::arrayToCsv($lines);
    }

    public function headings(): array
    {
        return [
            'Ano',
            'Mes',
            'Rede',
            'Loja',
            'CNPJ',
            'Tipo',
            'Meta',
            'Realizado'
        ];
    }

    private function collection($goals, $accomplished): array
    {
        $goalsAccomplished = [];

        foreach ($accomplished as $realized) {
            foreach ($goals as $goal) {
                if ($realized['cnpj'] == $goal->pointOfSale->cnpj) {
                    array_push($goalsAccomplished, [
                        data_get($goal, 'year', '-'),
                        data_get($goal, 'month', '-'),
                        data_get($goal, 'pointOfSale.tradingName', '-'),
                        data_get($goal, 'pointOfSale.label', '-'),
                        data_get($goal, 'pointOfSale.cnpj', '-'),
                        data_get($goal, 'goalType.type', '-'),
                        data_get($goal, 'goal', '-'),
                        data_get($realized, 'goals.'.$goal->month, '-')
                    ]);
                }
            }
        }
        return $goalsAccomplished;
    }
}
