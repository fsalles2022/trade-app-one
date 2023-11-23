<?php

namespace TradeAppOne\Domain\Factories;

use Buyback\Services\MountNewAttributesFromTradeIn;
use ClaroBR\Services\MountNewAttributeFromSiv;
use Generali\Services\MountNewAttributeFromGenerali;
use Mapfre\Services\MountNewAttributeFromMapfre;
use McAfee\Services\MountNewAttributeFromMcAfee;
use Movile\Services\MountNewAttributesFromMovile;
use NextelBR\Services\MountNewAttributesFromNextel;
use OiBR\Assistance\MountNewAttributeFromOiBR;
use SurfPernambucanas\Services\MountNewAttributeFromSurf;
use SurfPernambucanas\Services\MountNewAttributeFromSurfCorreios;
use TimBR\Services\MountNewAttributesFromTim;
use TradeAppOne\Domain\Enumerators\Operations;
use Uol\Services\MountNewAttributesFromUol;
use VivoBR\Services\MountNewAttributeFromSun;

class PriceFactory
{
    public const PRICE = [
        Operations::CLARO              => MountNewAttributeFromSiv::class,
        Operations::VIVO               => MountNewAttributeFromSun::class,
        Operations::MAPFRE             => MountNewAttributeFromMapfre::class,
        Operations::MCAFEE             => MountNewAttributeFromMcAfee::class,
        Operations::OI                 => MountNewAttributeFromOiBR::class,
        Operations::TIM                => MountNewAttributesFromTim::class,
        Operations::MOVILE             => MountNewAttributesFromMovile::class,
        Operations::NEXTEL             => MountNewAttributesFromNextel::class,
        Operations::TRADE_IN_MOBILE    => MountNewAttributesFromTradeIn::class,
        Operations::UOL                => MountNewAttributesFromUol::class,
        Operations::GENERALI           => MountNewAttributeFromGenerali::class,
        Operations::SURF_PERNAMBUCANAS => MountNewAttributeFromSurf::class,
        Operations::SURF_CORREIOS      => MountNewAttributeFromSurfCorreios::class,
    ];

    public static function make(array $service): array
    {
        $instance   = app()->make(self::PRICE[$service['operator']]);
        $attributes =  $instance->getAttributes($service);
        return array_merge($service, $attributes);
    }
}
