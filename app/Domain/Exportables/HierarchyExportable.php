<?php


namespace TradeAppOne\Domain\Exportables;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;

class HierarchyExportable
{
    protected $hierarchies;

    public function __construct(Collection $hierarchies)
    {
        $this->hierarchies = $hierarchies;
    }

    public function export()
    {
        $lines = [];
        array_push($lines, $this->headings());
        foreach ($this->hierarchies as $hierarchy) {
            array_push($lines, $this->collection($hierarchy));
        }
        return CsvHelper::arrayToCsv($lines);
    }

    public function headings()
    {
        return [
            'Regional',
            'Slug',
            'Rede'
        ];
    }

    public function collection($hierarchies)
    {
        return [
            data_get($hierarchies, 'label', ''),
            data_get($hierarchies, 'slug', ''),
            data_get($hierarchies, 'network.label', ''),
        ];
    }
}
