<?php

declare(strict_types=1);

namespace ClaroBR\Reports\Adapters;

use ClaroBR\Reports\Headers\ExternalSalesCsvHeaders;
use DateTime;
use Exception;
use Illuminate\Support\Arr;
use TradeAppOne\Domain\Enumerators\Modes;

class ExternalSalesMap
{
    /**
     * @param mixed[] $sales
     * @return mixed[]
     */
    public static function recordsToArray(array $sales): array
    {
        return array_merge(ExternalSalesCsvHeaders::headings(), self::collection($sales));
    }

    /**
     * @param mixed[] $sales
     * @return mixed[]
     */
    private static function collection(array $sales): array
    {
        $rows = [];

        foreach ($sales as $sale) {
            $rows[] = self::adaptRow($sale);
        }

        return $rows;
    }

    /**
     * @param mixed[] $sale
     * @return string[]
     * @throws Exception
     */
    private static function adaptRow(array $sale): array
    {
        $mode           = Arr::get($sale, 'mode', Modes::ACTIVATION);
        $dateIndication = self::formatDate(new DateTime($sale['createdAt']));
        $hourIndication = self::formatHour(new DateTime($sale['createdAt']));

        return [
            $mode === Modes::ACTIVATION ? 'Ativação' : 'Portabilidade',
            Arr::get($sale, 'areaCode', '-'),
            Arr::get($sale, 'msisdn', '-'),
            Arr::get($sale, 'iccid', '-'),
            Arr::get($sale, 'customerCpf', '-'),
            Arr::get($sale, 'customerEmail', '-'),
            Arr::get($sale, 'salesmanCpf', '-'),
            Arr::get($sale, 'salesmanName', '-'),
            Arr::get($sale, 'salesmanAreaCode', '-'),
            Arr::get($sale, 'pointOfSaleCode', '-'),
            Arr::get($sale, 'pointOfSaleName', '-'),
            Arr::get($sale, 'pointOfSaleHierarchyId', '-'),
            Arr::get($sale, 'pointOfSaleHierarchyName', '-'),
            Arr::get($sale, 'networkSlug', '-'),
            $dateIndication,
            $hourIndication
        ];
    }


    /**
     * @param DateTime $date
     * @return string
     */
    private static function formatDate(DateTime $date): string
    {
        return $date->format('d/m/Y');
    }

    /**
     * @param DateTime $hour
     * @return string
     */
    private static function formatHour(DateTime $hour): string
    {
        return $hour->format('H:i:s');
    }
}
