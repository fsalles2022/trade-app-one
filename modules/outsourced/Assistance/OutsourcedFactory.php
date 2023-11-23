<?php

namespace Outsourced\Assistance;

use Outsourced\Crafts\Services\DevicesGeneralService;
use Outsourced\Enums\Crafts;
use Outsourced\Enums\Outsourced;
use Outsourced\Exceptions\OutsourcedExceptions;
use Outsourced\Riachuelo\Services\DevicesRiachueloService;
use Outsourced\ViaVarejo\Services\TriangulationViaVarejoService;
use Outsourced\ViaVarejo\Services\CustomerViaVarejoService;
use SurfPernambucanas\Services\PagtelCustomerService;

class OutsourcedFactory
{
    private const SERVICES = [
        Outsourced::RIACHUELO => [
            Crafts::DEVICES => DevicesRiachueloService::class,
        ],
        Outsourced::VIA_VAREJO => [
            Crafts::DEVICES => DevicesGeneralService::class,
            Crafts::TRIANGULATION => TriangulationViaVarejoService::class,
            Crafts::CUSTOMER => CustomerViaVarejoService::class
        ],
        Outsourced::SURF_PERNAMBUCANAS => [
            Crafts::CUSTOMER => PagtelCustomerService::class
        ],
        Outsourced::GPA => [
            Crafts::DEVICES => DevicesGeneralService::class
        ],
        Outsourced::EXTRA => [
            Crafts::DEVICES => DevicesGeneralService::class
        ],
        Outsourced::MULTISOM => [
            Crafts::DEVICES => DevicesGeneralService::class
        ],
        Outsourced::SCHUMANN => [
            Crafts::DEVICES => DevicesGeneralService::class
        ]
    ];

    public static function make(?string $network, string $craft, bool $exception = true)
    {
        if (isset(self::SERVICES[$network][$craft])) {
            return resolve(self::SERVICES[$network][$craft]);
        }

        throw_if($exception, OutsourcedExceptions::serviceNotFound());
        return false;
    }

    public static function hasIntegration(string $network, string $craft): bool
    {
        return isset(self::SERVICES[$network][$craft]);
    }
}
