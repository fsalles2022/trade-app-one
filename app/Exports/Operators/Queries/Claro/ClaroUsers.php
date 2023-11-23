<?php
namespace TradeAppOne\Exports\Operators\Queries\Claro;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Exports\Operators\Traits\OperatorsFilter;

class ClaroUsers
{
    use OperatorsFilter;

    public function export($parameters): \League\Csv\Writer
    {
        $lines   = [];
        $lines[] = self::header();
        $lines   = array_merge($lines, self::adapter($this->resumeToExport($parameters)));
        return CsvHelper::arrayToCsv($lines);
    }

    private static function header(): array
    {
        return [
            'Nome Completo',
            'CPF',
            'Funcao',
            'Codigo do PDV',
            'Canal'
        ];
    }

    private static function adapter($users): array
    {
        $adapter = [];
        foreach ($users as $user) {
            foreach ($user->pointsOfSale as $pointOfSale) {
                if (data_get($pointOfSale, 'providerIdentifiers.CLARO')) {
                    $adapter[] = [
                        $user->firstName . ' ' . $user->lastName,
                        $user->cpf,
                        self::setUserFunction($user),
                        data_get($pointOfSale, 'providerIdentifiers.CLARO'),
                        $user->pointsOfSale->first()->network->id === 4 ? 'CLARO_AA' : 'CLARO_VAREJO',
                    ];
                }
            }
        }
        return $adapter;
    }

    private static function setUserFunction($user): string
    {
        $roles = [
            'consultor',
            'associado',
            'lider',
            'vendedor',
            'operador',
            'quiosque'
        ];

        return str_contains($user->role->slug, $roles) ? 'VENDEDOR' : 'LIDER_SETOR';
    }

    public function resumeToExport($parameters): Collection
    {
        return $this->filter($parameters, Operations::CLARO)
            ->with('pointsOfSale:providerIdentifiers,slug,networkId', 'pointsOfSale.network:label,id', 'role:slug,id')
            ->select('id', 'firstName', 'lastName', 'cpf', 'roleId')
            ->get();
    }
}
