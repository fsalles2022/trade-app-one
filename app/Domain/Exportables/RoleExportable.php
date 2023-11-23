<?php


namespace TradeAppOne\Domain\Exportables;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;

class RoleExportable
{
    protected $roles;

    public function __construct(Collection $roles)
    {
        $this->roles = $roles;
    }

    public function export()
    {
        $lines = [];
        array_push($lines, $this->headings());
        foreach ($this->roles as $role) {
            array_push($lines, $this->collection($role));
        }
        return CsvHelper::arrayToCsv($lines);
    }

    public function headings(): array
    {
        return [
            'Rede',
            'Funcao',
            'Slug'
        ];
    }

    public function collection($role)
    {
        return [
            data_get($role, 'network.label', ''),
            data_get($role, 'name', ''),
            data_get($role, 'slug', '')
        ];
    }
}
