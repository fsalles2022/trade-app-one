<?php


namespace Buyback\Components\Vouchers\Factories;

use Buyback\Exceptions\GeneratorVoucherException;
use Buyback\Resources\contracts\VoucherLayout;
use Buyback\Resources\contracts\Vouchers\Cea\VoucherCeaLayout;
use Buyback\Resources\contracts\Vouchers\Iplace\VoucherIplaceLayout;
use Buyback\Resources\contracts\Vouchers\IplaceIpad\VoucherIplaceIpadLayout;
use Buyback\Resources\contracts\Vouchers\IplaceAndroid\VoucherIplaceAndroidLayout;
use Buyback\Resources\contracts\Vouchers\Riachuelo\VoucherRiachueloLayout;
use Buyback\Resources\contracts\Vouchers\VoucherBase;
use Buyback\Resources\contracts\Vouchers\Watch\VoucherWatchLayout;
use Exception;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Service;

class TradeInVoucherFactory
{
    private const DEFAULT = 'default';

    public const AVAILABLE = [
        NetworkEnum::IPLACE => [
            Operations::IPLACE => VoucherIplaceLayout::class,
            Operations::IPLACE_ANDROID => VoucherIplaceAndroidLayout::class,
            Operations::IPLACE_IPAD => VoucherIplaceIpadLayout::class,
            Operations::WATCH => VoucherWatchLayout::class
        ],
        NetworkEnum::CEA => VoucherCeaLayout::class,
        NetworkEnum::RIACHUELO => VoucherRiachueloLayout::class,
        self::DEFAULT       => VoucherLayout::class
    ];

    public static function run(Service $service): string
    {
        try {
            return self::generateVoucher($service);
        } catch (Exception $exception) {
            throw new GeneratorVoucherException();
        }
    }

    private static function generateVoucher(Service $service): string
    {
        return self::instanceVoucherLayout($service)->toPdf();
    }

    private static function instanceVoucherLayout(Service $service):VoucherBase
    {
        $network      = self::getNetwork($service);
        $serviceArray = $service->toArray();
        $saleEntity   = $service->sale;
        $available    = self::AVAILABLE[$network];
        
        if (is_array($available)) {
            $available = self::getOperation($service, $network);
        }
        
        return (new $available($serviceArray, $saleEntity));
    }

    private static function getNetwork(Service $service) : string
    {
        $sale    = $service->sale;
        $network = data_get($sale, 'pointOfSale.network.slug');
        return (isset(self::AVAILABLE[$network]))
            ? $network
            : self::DEFAULT;
    }

    private static function getOperation(Service $service, string $network)
    {
        $serviceOperation = data_get($service, 'operation');
        return self::AVAILABLE[$network][$serviceOperation] ?? self::DEFAULT;
    }
}
