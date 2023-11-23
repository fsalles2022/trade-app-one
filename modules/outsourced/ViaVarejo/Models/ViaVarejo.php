<?php


namespace Outsourced\ViaVarejo\Models;

use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;

class ViaVarejo extends Service
{
    public static function getService(string $serviceTransaction): ?Service
    {
        return Sale::where('services.serviceTransaction', $serviceTransaction)
            ->first()
            ->services()
            ->where('serviceTransaction', $serviceTransaction)
            ->whereIn('status', [ServiceStatus::APPROVED, ServiceStatus::ACCEPTED, ServiceStatus::CANCELED])
            ->first();
    }

    public static function updateLog(Service $service): void
    {
        if ($status = self::chooseStatus($service)) {
            $logs               = $service->log ?? [];
            $logs['syncStatus'] = $status;

            $service->forceFill(['log' => $logs]);

            $service->touch();
            $service->sale->touch();
            $service->save();
        }
    }

    public static function chooseStatus(Service $service): ?string
    {
        $status = [
            ServiceStatus::APPROVED => ServiceStatus::SUBMITTED,
            ServiceStatus::ACCEPTED => ServiceStatus::PENDING_SUBMISSION,
            ServiceStatus::CANCELED => ServiceStatus::SUBMITTED
        ];

        return $status[$service->status] ?? null;
    }
}
