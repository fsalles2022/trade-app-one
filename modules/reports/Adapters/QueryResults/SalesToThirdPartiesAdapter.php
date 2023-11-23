<?php

namespace Reports\Adapters\QueryResults;

use Illuminate\Support\Collection;

class SalesToThirdPartiesAdapter
{
    public static function adapt(Collection $collection): array
    {
        $pointsOfSale = data_get($collection, 'aggregations.pointsOfSale.buckets');
        return self::adaptPointsOfSale($pointsOfSale);
    }

    private static function adaptPointsOfSale(array $pointsOfSale): array
    {
        return array_map(function ($pointOfSale) {
            $users = data_get($pointOfSale, 'users.buckets');
            return [
                'cnpj'  => $pointOfSale['key'],
                'total' => $pointOfSale['doc_count'],
                'users' => self::adaptUsers($users)
            ];
        }, $pointsOfSale);
    }

    private static function adaptUsers(array $users): array
    {
        return array_map(function ($user) {
            $operators = data_get($user, 'operators.buckets');
            return [
                'cpf'       => $user['key'],
                'total'     => $user['doc_count'],
                'operators' => self::adaptOperators($operators)
            ];
        }, $users);
    }

    private static function adaptOperators(array $operators): array
    {
        return array_map(function ($operator) {
            return [
                'operator' => $operator['key'],
                'total'    => $operator['doc_count'],
                'pre'      => data_get($operator, 'CONTROLE.doc_count'),
                'pos'      => data_get($operator, 'POS_PAGO.doc_count'),
                'controle' => data_get($operator, 'PRE_PAGO.doc_count')
            ];
        }, $operators);
    }
}
