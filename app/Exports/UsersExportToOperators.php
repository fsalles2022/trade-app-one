<?php

namespace TradeAppOne\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class UsersExportToOperators implements
    FromCollection,
    WithHeadings
{
    use Exportable;
    private $usersCollection;
    private $columns;

    public function __construct(Collection $usersCollection, array $columns)
    {
        $this->usersCollection = $usersCollection;
        $this->columns         = $columns;
    }

    public function collection()
    {
        return $this->usersCollection;
    }

    public function headings(): array
    {
        return $this->columns;
    }
}
