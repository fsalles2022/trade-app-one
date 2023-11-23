<?php

declare(strict_types=1);

namespace Outsourced\Pernambucanas\Hooks;

use Illuminate\Http\Response;
use Outsourced\Pernambucanas\Adapters\PayloadAdapter;
use Outsourced\Pernambucanas\Connections\PernambucanasConnection;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Services\NetworkHooks\NetworkHook;
use TradeAppOne\Domain\Enumerators\Operations;

class PernambucanasHook implements NetworkHook
{
    private const STATUS_TO_SEND = [
        ServiceStatus::APPROVED,
        ServiceStatus::ACCEPTED
    ];

    /** @var PernambucanasConnection */
    private $pernambucanasConnection;

    public function __construct(PernambucanasConnection $pernambucanasConnection)
    {
        $this->pernambucanasConnection = $pernambucanasConnection;
    }

    public function execute(Service $service, array $options = []): bool
    {
        $status = data_get($service, 'status', '');

        if (in_array($status, self::STATUS_TO_SEND) &&
            $service->operation !== Operations::SURF_PERNAMBUCANAS_PRE_RECHARGE
        ) {
            return $this->sendSale($service);
        }
        return false;
    }

    private function sendSale(Service $service): bool
    {
        $response = $this->pernambucanasConnection->saleRegister(
            (new PayloadAdapter($service))
                ->adapt()
                ->toArray()
        );

        return $response->getStatus() === Response::HTTP_NO_CONTENT &&
            count($response->toArray()) === 0;
    }
}
