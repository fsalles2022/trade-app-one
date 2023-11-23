<?php

namespace Reports\SubModules\Hourly\Helpers;

use Illuminate\Support\Collection;
use Reports\Services\SalesByMonthAndPeriod;
use Reports\SubModules\Hourly\Constants\PrePosLineActivationOperations;
use Reports\SubModules\Hourly\Constants\OperationsByOperators;

class ConsolidateOperatorMapper
{
    public static function mapDminusByPosPago($groupOfOperations, array $saleByMontAndPeriodResult)
    {
        $dMinusOperators = $saleByMontAndPeriodResult[SalesByMonthAndPeriod::CONSOLIDATE_OPERATORS_DMINUS]['buckets'][0][SalesByMonthAndPeriod::OPERATORS]['buckets'];

        return self::countTotalByGroup($groupOfOperations, collect($dMinusOperators), PrePosLineActivationOperations::POS);
    }

    public static function mapDminusByPrePago($groupOfOperations, array $saleByMontAndPeriodResult)
    {
        $dMinusOperators = $saleByMontAndPeriodResult[SalesByMonthAndPeriod::CONSOLIDATE_OPERATORS_DMINUS]['buckets'][0][SalesByMonthAndPeriod::OPERATORS]['buckets'];

        return self::countTotalByGroup($groupOfOperations, collect($dMinusOperators), PrePosLineActivationOperations::PRE);
    }

    public static function mapDayByPosPago($groupOfOperations, array $saleByMontAndPeriodResult)
    {
        $dayOperators = $saleByMontAndPeriodResult[SalesByMonthAndPeriod::CONSOLIDATE_OPERATORS_DAY]['buckets'][0][SalesByMonthAndPeriod::OPERATORS]['buckets'];

        return self::countTotalByGroup($groupOfOperations, collect($dayOperators), PrePosLineActivationOperations::POS);
    }

    public static function mapDayByPrePago($groupOfOperations, array $saleByMontAndPeriodResult)
    {
        $dayOperators = $saleByMontAndPeriodResult[SalesByMonthAndPeriod::CONSOLIDATE_OPERATORS_DAY]['buckets'][0][SalesByMonthAndPeriod::OPERATORS]['buckets'];

        return self::countTotalByGroup($groupOfOperations, collect($dayOperators), PrePosLineActivationOperations::PRE);
    }

    public static function mapMonthByPrePago($groupOfOperations, array $saleByMontAndPeriodResult)
    {
        $monthOperators = $saleByMontAndPeriodResult[SalesByMonthAndPeriod::CONSOLIDATE_OPERATORS_MONTH]['buckets'][0];

        $mappedOperations = [];
        foreach ($groupOfOperations as $operator => $value) {
            if (self::existsOperationInGroupsOfOperations($operator)) {
                $operatorQuantity = self::countOperatorsByGroup(
                    $monthOperators,
                    $operator,
                    OperationsByOperators::PRE_PAGO
                );

                $mappedOperations[$operator] = $operatorQuantity;
            }
        }

        return $mappedOperations;
    }

    public static function mapValuesFromConsolidateOperations(array $saleByMontAndPeriodResult): float
    {
        $monthOperators = $saleByMontAndPeriodResult[SalesByMonthAndPeriod::CONSOLIDATE_OPERATORS_MONTH]['buckets'][0][SalesByMonthAndPeriod::OPERATORS]['buckets'];

        $sumOfValues = collect($monthOperators)
            ->pluck(SalesByMonthAndPeriod::OPERATIONS)
            ->pluck('buckets');


        $total = 0.0;
        foreach ($sumOfValues as $group) {
            foreach ($group as $operation) {
                $operationInFloatValue    = floatval($operation['prices']['value']);
                $valueInCents             = (int) ($operationInFloatValue * 100);
                $valueWithCorrectDecimals = $valueInCents / 100;

                $total += $valueWithCorrectDecimals;
            }
        }

        return $total;
    }

    public static function mapMonthByPosPago($groupOfOperations, array $saleByMontAndPeriodResult)
    {
        $monthOperators = $saleByMontAndPeriodResult[SalesByMonthAndPeriod::CONSOLIDATE_OPERATORS_MONTH]['buckets'][0];

        $mappedOperations = [];
        foreach ($groupOfOperations as $operator => $value) {
            if (self::existsOperationInGroupsOfOperations($operator)) {
                $operatorQuantity = self::countOperatorsByGroup(
                    $monthOperators,
                    $operator,
                    OperationsByOperators::POS_PAGO
                );

                $mappedOperations[$operator] = $operatorQuantity;
            }
        }

        return $mappedOperations;
    }

    public static function countOperatorsByGroup(array $monthOperators, $operator, $operationsByOperator): int
    {
        $resultOfOp = collect($monthOperators[SalesByMonthAndPeriod::OPERATORS]['buckets'])
            ->where('key', $operator)
            ->first();

        $operations = collect(data_get($resultOfOp, SalesByMonthAndPeriod::OPERATIONS . '.buckets', []));

        $posPago = $operations
            ->whereIn('key', data_get($operationsByOperator, $operator), $operator)
            ->sum('doc_count');

        return $posPago;
    }

    public static function countTotalByGroup($group, Collection $collectionPeriod, $groupOfOperations): int
    {
        $total = 0;
        foreach ($group as $operator => $value) {
            $resultOfOp = $collectionPeriod->where('key', $operator)->first();
            $operations = collect(data_get($resultOfOp, SalesByMonthAndPeriod::OPERATIONS . '.buckets', []));

            $total += $operations
                ->whereIn('key', $groupOfOperations)
                ->sum('doc_count');
        }

        return $total;
    }

    private static function existsOperationInGroupsOfOperations($operator): bool
    {
        $operatorExistsInGroup = in_array($operator, array_keys(array_merge(
            OperationsByOperators::PRE_PAGO,
            OperationsByOperators::POS_PAGO
        )));
        return $operatorExistsInGroup;
    }
}
