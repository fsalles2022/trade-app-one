<?php

namespace Reports\Helpers;

use Carbon\Carbon;

class ReportDateHelper
{
    public static function periodWithCriteriaMonthly(array $filters = [], bool $startDateMonthly = false): array
    {
        $startDate = data_get($filters, 'startDate');
        $endDate   = data_get($filters, 'endDate');

        if (empty($startDate) and empty($endDate)) {
            $startDate = now()->startOfMonth();
            $endDate   = now();
        } elseif (isset($startDate) and empty($endDate)) {
            $endDate = now();
        } elseif (empty($startDate) and isset($endDate)) {
            $startDate = $startDateMonthly ? Carbon::parse($endDate)->startOfMonth() : null;
        }

        return [
            'startDate' => $startDate ? Carbon::make($startDate)->format('d/m/y') : '',
            'endDate'   => Carbon::make($endDate)->format('d/m/y')
        ];
    }
}
