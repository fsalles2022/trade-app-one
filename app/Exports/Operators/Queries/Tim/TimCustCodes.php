<?php
namespace TradeAppOne\Exports\Operators\Queries\Tim;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;
use TradeAppOne\Domain\Models\Tables\PointOfSale;

class TimCustCodes
{
    public function export($networks): \League\Csv\Writer
    {
        $pointOfSales = self::collection($networks);
        return CsvHelper::arrayToCsv(self::adapter($pointOfSales));
    }

    private static function header(): array
    {
        return ['CUSTCODES', 'Rede'];
    }

    private static function collection($networks): Collection
    {
        return PointOfSale::query()->whereHas('network', function ($query) use ($networks) {
            if (filled($networks)) {
                $query->whereIn('slug', $networks);
            }
        })->with('network')->get();
    }

    private static function adapter($pointOfSales): array
    {
        $header = [];
        $lines  = [];
        foreach ($pointOfSales as $pointOfSale) {
            if (data_get($pointOfSale, 'providerIdentifiers.TIM')) {
                $map     = [data_get($pointOfSale, 'providerIdentifiers.TIM'), data_get($pointOfSale, 'network.slug')];
                $lines[] = $map;
            }
        }
        $header[] = self::header();
        return array_merge($header, $lines);
    }
}
