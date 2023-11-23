<?php


namespace Outsourced\GPA\Models;

use Outsourced\Enums\Outsourced;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;
use Illuminate\Database\Eloquent\Collection;

class GPA extends Service
{
    /**
     * @param string $key
     * @param $value
     * @param array $status
     * @return Collection|null
     */
    public static function getService(string $key, $value, array $status): ?Collection
    {
        $sale = Sale::where('services.' . $key, $value)
            ->where('pointOfSale.network.slug', Outsourced::GPA)->first();

        return ($sale instanceof Sale)
            ? $sale->services()->where($key, $value)->whereIn('status', $status)
            : null;
    }

    /**
     * @param Service $service
     * @param array $attributes
     */
    public static function updateLog(Service $service, array $attributes): void
    {
        $logs   = $service->log ?? [];
        $logs[] = $attributes;

        self::updateAttributes($service, ['log' => $logs]);
    }

    /**
     * @param Service $service
     * @param array $attributes
     */
    public static function updateAttributes(Service $service, array $attributes): void
    {
        $service->forceFill($attributes);
        $service->touch();
        $service->sale->touch();
        $service->save();
    }
}
