<?php
namespace TradeAppOne\Exports\Operators\Queries\Claro;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;
use TradeAppOne\Domain\Models\Tables\PointOfSale;

class ClaroCustCodes
{
    public function export($networks): \League\Csv\Writer
    {
        $pointOfSales = self::collection($networks);
        return CsvHelper::arrayToCsv(self::adapter($pointOfSales));
    }

    public static function header(): array
    {
        return [
            'Codigo do Agente',
            'Razao Social',
            'Nome do PDV',
            'cnpj',
            'Nome Fantasia',
            'Logradouro',
            'Numero',
            'Bairro',
            'CEP',
            'Cidade',
            'uf',
            'DDD',
            'Rede',
            'Regional'
        ];
    }

    private static function adapter($pointsOfSale): array
    {
        $lines  = [];
        $header = [];
        foreach ($pointsOfSale as $pointOfSale) {
            if (data_get($pointOfSale, 'providerIdentifiers.CLARO')) {
                $lines[] = [
                    data_get($pointOfSale, 'providerIdentifiers.CLARO'),
                    $pointOfSale->slug,
                    $pointOfSale->label,
                    $pointOfSale->cnpj,
                    $pointOfSale->tradingName,
                    $pointOfSale->local,
                    $pointOfSale->number,
                    $pointOfSale->neighborhood,
                    $pointOfSale->zipCode,
                    $pointOfSale->city,
                    $pointOfSale->state,
                    $pointOfSale->areaCode,
                    $pointOfSale->network->slug,
                    self::regional($pointOfSale->areaCode)
                ];
            }
        }
        $header[] = self::header();
        return array_merge($header, $lines);
    }

    private static function collection($networks): Collection
    {
        return PointOfSale::query()->whereHas('network', static function ($query) use ($networks) {
            if (filled($networks)) {
                $query->whereIn('slug', $networks);
            }
        })
            ->with('network:slug,id')
            ->select('id', 'slug', 'label', 'cnpj', 'tradingName', 'local', 'number', 'areaCode', 'neighborhood', 'zipCode', 'city', 'state', 'providerIdentifiers', 'networkId')
            ->get();
    }

    private static function regional($areaCode): string
    {
        $regional = [
            'SP1'   => ['11'],
            'SP2'   => ['12', '13', '14', '15', '16', '17', '18', '19'],
            'RJ/ES' => ['21', '21 - 1', '21 - 2', '22', '24', '27', '28'],
            'MG'    => ['31', '32', '33', '34', '35', '37', '38'],
            'PR/SC' => ['41', '42', '43', '44', '45', '46', '47', '48', '49'],
            'RS'    => ['51', '53', '54', '55'],
            'CO'    => ['61', '62', '63', '64', '65', '66', '67', '68', '69'],
            'BA/SE' => ['71', '73', '74', '75', '77', '79'],
            'NE'    => ['81', '82', '83', '84', '85', '86', '87', '88', '89'],
            'NO'    => ['91', '92', '93', '94', '95', '96', '97', '98', '99']
        ];
        foreach ($regional as $key => $values) {
            if (in_array($areaCode, $values, true)) {
                return $key;
            }
        }
        return ' ';
    }
}
