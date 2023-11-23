<?php

declare(strict_types=1);

namespace TimBR\Services;

use TimBR\Connection\TimBRConnection;
use TimBR\Enumerators\TimBRStatus;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Services\ContestBehavior;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Exceptions\BusinessExceptions\InvalidServiceStatus;
use TradeAppOne\Exceptions\BusinessExceptions\NetworkNotFoundException;
use TradeAppOne\Exceptions\BusinessExceptions\ProtocolNoExists;
use TradeAppOne\Exceptions\BusinessExceptions\TimStatusNoExists;

class TimBRContest implements ContestBehavior
{
    protected $connection;

    /**
     * @var SaleService
     */
    private $saleService;

    /**
     * @param TimBRConnection $connection
     * @param SaleService $saleService
     */
    public function __construct(TimBRConnection $connection, SaleService $saleService)
    {
        $this->connection  = $connection;
        $this->saleService = $saleService;
    }

    /**
     * @param Service $service
     * @param array $payload
     * @return Service
     * @throws TimStatusNoExists|InvalidServiceStatus|ProtocolNoExists|NetworkNotFoundException
     */
    public function contestService(Service $service, array $payload = []): Service
    {
        $protocol = data_get($service, 'operatorIdentifiers.protocol');
        $network  = data_get($service, 'sale.pointOfSale.network.slug');

        if ($this->validateParameter($protocol)) {
            $this->saleService->pushLogService($service, ['protocolNoExists' => 'Protocol is empty']);
            throw new ProtocolNoExists();
        }

        if ($this->validateParameter($network)) {
            $this->saleService->pushLogService($service, ['networkNotFoundException' => 'Network is empty']);
            throw new NetworkNotFoundException();
        }

        $statusFromTim = $this->getServiceByProtocol($network, $protocol);

        if ($this->validateParameter($statusFromTim)) {
            $this->saleService->pushLogService($service, ['timStatusNoExists' => 'Status from tim is empty']);
            throw new TimStatusNoExists();
        }

        return $this->updateStatus($statusFromTim, $service);
    }

    /**
     * @param $parameter
     * @return bool
     */
    private function validateParameter($parameter): bool
    {
        return empty($parameter);
    }

    /**
     * @param string $network
     * @param $protocol
     * @return array
     */
    private function getServiceByProtocol(string $network, $protocol): array
    {
        return $this->connection
            ->selectCustomConnection($network)
            ->getOrderStatusByProtocol($protocol)
            ->toArray();
    }

    /**
     * @param array $statusFromTim
     * @param Service $service
     * @return Service
     * @throws InvalidServiceStatus
     */
    private function updateStatus(array $statusFromTim, Service $service): Service
    {
        $toUpdate = self::translateStatus($statusFromTim);

        if (filled($toUpdate)) {
            $this->saleService->pushLogService($service, ['contest' => $toUpdate]);
            return $this->saleService->updateService($service, $toUpdate);
        }

        $this->saleService->pushLogService($service, ['InvalidServiceStatus' => 'Status from tim is invalid']);
        throw new InvalidServiceStatus();
    }

    /**
     * @param array $serviceFromTim
     * @return array
     */
    private static function translateStatus(array $serviceFromTim): array
    {
        $toUpdate = [];
        if (in_array(data_get($serviceFromTim, 'status', ''), TimBRStatus::APPROVED, true)) {
            $toUpdate['statusThirdParty'] = data_get($serviceFromTim, 'status', '');
            $toUpdate['status']           = ServiceStatus::APPROVED;
        } elseif (in_array(data_get($serviceFromTim, 'status', ''), TimBRStatus::CANCELED, true)) {
            $toUpdate['statusThirdParty'] = data_get($serviceFromTim, 'status', '');
            $toUpdate['status']           = ServiceStatus::CANCELED;
        } elseif (in_array(data_get($serviceFromTim, 'status', ''), TimBRStatus::REJECTED, true)) {
            $toUpdate['statusThirdParty'] = data_get($serviceFromTim, 'status', '');
            $toUpdate['status']           = ServiceStatus::REJECTED;
        } elseif (in_array(data_get($serviceFromTim, 'status', ''), TimBRStatus::ACCEPTED, true)) {
            $toUpdate['statusThirdParty'] = data_get($serviceFromTim, 'status', '');
            $toUpdate['status']           = ServiceStatus::ACCEPTED;
        }
        return $toUpdate;
    }
}
