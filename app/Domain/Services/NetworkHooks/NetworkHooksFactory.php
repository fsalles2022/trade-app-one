<?php

namespace TradeAppOne\Domain\Services\NetworkHooks;

use Exception;
use Outsourced\CasaEVideo\Hooks\CasaEVideoHook;
use Outsourced\GPA\Hooks\GPAHooks;
use Outsourced\Pernambucanas\Hooks\PernambucanasHook;
use Outsourced\ViaVarejo\Hooks\ViaVarejoHooks;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;

class NetworkHooksFactory
{
    public const AVAILABLE = [
        NetworkEnum::VIA_VAREJO    => ViaVarejoHooks::class,
        NetworkEnum::GPA           => GPAHooks::class,
        NetworkEnum::EXTRA         => GPAHooks::class,
        NetworkEnum::PERNAMBUCANAS => PernambucanasHook::class,
        NetworkEnum::CASAEVIDEO    => CasaEVideoHook::class
        // NetworkEnum::CEA => CeaHooks::class
    ];

    public static function run(Service $service): void
    {
        $sale    = $service->sale;
        $network = data_get($sale, 'pointOfSale.network.slug');

        if (self::hookExists($network)) {
            try {
                NetworkHookJob::dispatchNow(self::AVAILABLE[$network], $service);
            } catch (Exception $exception) {
                $saleRepo = resolve(SaleRepository::class);
                $saleRepo->pushLogService($service, ['errorNetworkHooksFactory' => $exception->getMessage()]);
            }
        }
    }

    public static function hookExists(?string $network): bool
    {
        if (array_key_exists($network, self::AVAILABLE)) {
            return isset(self::AVAILABLE[$network]);
        }
        return false;
    }
}
