<?php


namespace Outsourced\ViaVarejo\Hooks;

use Outsourced\ViaVarejo\Adapters\Request\ActivationAdapter;
use Outsourced\ViaVarejo\Adapters\Request\MigrationAdapter;
use Outsourced\ViaVarejo\Adapters\Request\PortabilityAdapter;
use Outsourced\ViaVarejo\Connections\ViaVarejoConnection;
use Outsourced\ViaVarejo\DataTransferObjects\ViaVarejoBase;
use Outsourced\ViaVarejo\Exceptions\ViaVarejoExceptions;
use Outsourced\ViaVarejo\Models\ViaVarejo;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Services\NetworkHooks\NetworkHook;

class ViaVarejoHooks implements NetworkHook
{
    protected $viaVarejoConnection;

    public const MODES = [
        Modes::MIGRATION => MigrationAdapter::class,
        Modes::PORTABILITY => PortabilityAdapter::class,
        Modes::ACTIVATION => ActivationAdapter::class
    ];

    public function __construct(ViaVarejoConnection $viaVarejoConnection)
    {
        $this->viaVarejoConnection = $viaVarejoConnection;
    }

    public function execute(Service $service, array $options = []): void
    {
        $viaVarejoService = ViaVarejo::getService($service->serviceTransaction);

        if ($viaVarejoService === null) {
            return;
        }

        $payload = $this->makeAdapter($viaVarejoService)->toArray();

        $this->viaVarejoConnection->saveSale($payload);
        ViaVarejo::updateLog($viaVarejoService);
    }

    public function makeAdapter(Service $service): ViaVarejoBase
    {
        throw_if(empty(self::MODES[$service->mode]), ViaVarejoExceptions::serviceNotFound());

        return  app()->makeWith(self::MODES[$service->mode], ['service'=> $service]);
    }
}
