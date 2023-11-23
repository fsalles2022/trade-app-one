<?php

declare(strict_types=1);

namespace Outsourced\CasaEVideo\Hooks;

use Outsourced\CasaEVideo\Connection\CasaEVideoConnection;
use Outsourced\Pernambucanas\Adapters\PayloadAdapter;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Services\NetworkHooks\NetworkHook;

class CasaEVideoHook implements NetworkHook
{
    /** @var CasaEVideoConnection */
    private $casaEVideoConnection;

    public function __construct(CasaEVideoConnection $casaEVideoConnection)
    {
        $this->casaEVideoConnection = $casaEVideoConnection;
    }

    /** @param mixed[] $options */
    public function execute(Service $service, array $options = []): bool
    {
        $response = $this->casaEVideoConnection->sendSale(
            (new PayloadAdapter($service))
                ->adapt()
                ->toArray()
        );

        return $response->isSuccess() && count($response->toArray()) === 0;
    }
}
