<?php

namespace TradeAppOne\Domain\Repositories\Collections;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use TradeAppOne\Domain\Models\Collections\RefusedSale;

class RefusedSaleRepository
{
    public static function getByFilter(array $parameters): Builder
    {
        $refused = RefusedSale::query();
        foreach ($parameters as $key => $value) {
            switch ($key) {
                case 'startDate':
                    $refused->where('createdAt', '>=', Carbon::parse($value, 'America/Sao_Paulo'));
                    break;
                case 'endDate':
                    $refused->where('createdAt', '<=', Carbon::parse($value, 'America/Sao_Paulo'));
                    break;
                default:
                    $refused->where($key, 'like', "%$value%");
                    break;
            }
        }
        return $refused;
    }

    public static function getToExport(array $parameters): array
    {
        $refused = self::getByFilter($parameters)->get()->toArray();
        return self::mapDatatoCsv($refused);
    }

    private static function mapDatatoCsv(array $data)
    {
        $mapped = [
            ['Data de referência', 'Nome Cliente', 'CPF Cliente', 'Email Cliente', 'Linha']
        ];
        foreach ($data as $row) {
            $mapped[] = [
                Carbon::createFromFormat('Y-m-d H:i:s', $row['referenceDate'])->format('d/m/Y H:i:s'),
                $row['clientName'],
                $row['clientCpf'],
                $row['clientEmail'],
                $row['clientNumber'],
            ];
        }
        return $mapped;
    }
}
