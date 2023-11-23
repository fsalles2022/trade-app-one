<?php

namespace Reports\SubModules\Hourly\Helpers;

use Reports\SubModules\Hourly\Constants\HourConstants;

class HeaderMapper
{
    public static function map(
        array $availableServices,
        array $elasticResult,
        CriteriaHourlyDminus $strategyCriteria
    ): array {
        $posPagoScaffold  = ScaffoldToGroupOfOperators::createPosPagoWithCustomOperation(
            $availableServices,
            [
                HourConstants::PERCENT => 0,
                HourConstants::QUANTITY => 0
            ]
        );
        $posPagoOperators = ConsolidateOperatorMapper::mapMonthByPosPago($posPagoScaffold, $elasticResult);
        $posPagoDay       = ConsolidateOperatorMapper::mapDayByPosPago($posPagoScaffold, $elasticResult);
        $posPagoDminus    = ConsolidateOperatorMapper::mapDminusByPosPago($posPagoScaffold, $elasticResult);

        $prePagoScaffold = ScaffoldToGroupOfOperators::createPrePagoWithCustomOperation(
            $availableServices,
            [
                HourConstants::PERCENT => 0,
                HourConstants::QUANTITY => 0
            ]
        );

        $prePagoOperators = ConsolidateOperatorMapper::mapMonthByPrePago($prePagoScaffold, $elasticResult);
        $prePagoDay       = ConsolidateOperatorMapper::mapDayByPrePago($prePagoScaffold, $elasticResult);
        $prePagoDminus    = ConsolidateOperatorMapper::mapDminusByPrePago($prePagoScaffold, $elasticResult);

        $posPagoOperatorsTotal = array_sum($posPagoOperators);
        $prePagoOperatorsTotal = array_sum($prePagoOperators);

        $posPagoOperators = self::getOperatorDetail($posPagoScaffold, $posPagoOperators, $posPagoOperatorsTotal);
        $prePagoOperators = self::getOperatorDetail($prePagoScaffold, $prePagoOperators, $prePagoOperatorsTotal);

        $consolidateMonthValues = ConsolidateOperatorMapper::mapValuesFromConsolidateOperations($elasticResult);

        $totalOperatorsMonth = $posPagoOperatorsTotal + $prePagoOperatorsTotal;

        $structure = [
            'RESUME' => [
                'TOTAL' => [
                    'QUANTITY' => $totalOperatorsMonth,
                    'PERCENT' => '100%',
                    'VALUES' => $consolidateMonthValues,
                ]
            ],
            'POS_PAGO' => array_merge(
                $posPagoOperators,
                [
                    'TOTAL' => [
                        HourConstants::QUANTITY => $posPagoOperatorsTotal,
                        HourConstants::PERCENT => self::calculatePercent($posPagoOperatorsTotal, $totalOperatorsMonth)
                    ],
                    'DMINUS' . $strategyCriteria->strategy => [
                        HourConstants::QUANTITY => $posPagoDminus,
                        HourConstants::PERCENT => $strategyCriteria->dMinusStart->format('d/m')
                    ],
                    'DAY' => [
                        HourConstants::QUANTITY => $posPagoDay,
                        HourConstants::PERCENT => $strategyCriteria->day->format('d/m'),
                    ],
                ]
            ),
            'PRE_PAGO' => array_merge(
                $prePagoOperators,
                [
                    'TOTAL' => [
                        HourConstants::QUANTITY => $prePagoOperatorsTotal,
                        HourConstants::PERCENT => self::calculatePercent($prePagoOperatorsTotal, $totalOperatorsMonth)
                    ],
                    'DMINUS' . $strategyCriteria->strategy => [
                        HourConstants::QUANTITY => $prePagoDminus,
                        HourConstants::PERCENT => $strategyCriteria->dMinusStart->format('d/m')
                    ],
                    'DAY' => [
                        HourConstants::QUANTITY => $prePagoDay,
                        HourConstants::PERCENT => $strategyCriteria->day->format('d/m'),
                    ],
                ]
            ),
        ];

        return $structure;
    }

    private static function calculatePercent($actual, $total)
    {
        if ($total > 0) {
            return round(($actual / $total) * 100, 2);
        } else {
            return 0;
        }
    }

    private static function getOperatorDetail(array $operators, $data, $total)
    {
        $details = [];

        foreach ($operators as $operator => $value) {
            $valueFromOperator                           = $data[$operator] ?? 0.0;
            $details[$operator][HourConstants::QUANTITY] = $valueFromOperator;
            $details[$operator][HourConstants::PERCENT]  = self::calculatePercent(
                $valueFromOperator,
                $total
            );
        }

        return $details;
    }
}
