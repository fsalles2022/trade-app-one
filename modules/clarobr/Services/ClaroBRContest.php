<?php

namespace ClaroBR\Services;

use ClaroBR\Adapters\AdaptStatusFromSiv;
use ClaroBR\Exceptions\ClaroExceptions;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Services\ContestBehavior;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Exceptions\BusinessExceptions\ServiceNotAvailableToContest;
use TradeAppOne\Exceptions\BusinessExceptions\ServiceNotIntegrated;

class ClaroBRContest implements ContestBehavior
{
    const DONT_NEED_CONTEST = [ServiceStatus::ACCEPTED, ServiceStatus::APPROVED];
    protected $serviceService;
    protected $sivService;

    public function __construct(SivService $sivService, SaleService $serviceService)
    {
        $this->serviceService = $serviceService;
        $this->sivService     = $sivService;
    }

    public function contestService(Service $service, array $payload = [])
    {
        $identifiers = data_get($service, 'operatorIdentifiers');
        $userId      = data_get($service, 'sale.user.id');

        if (is_null($identifiers)) {
            throw new ServiceNotIntegrated();
        }

        if ($service->operation === Operations::CLARO_CONTROLE_FACIL) {
            throw new ServiceNotAvailableToContest();
        }

        $serviceFromSiv = data_get($this->sivService->getSale($identifiers)->first(), 'services', []);

        if ($serviceFromSiv = collect($serviceFromSiv)->where('id', $identifiers['servico_id'])->first()) {
            $toUpdate = AdaptStatusFromSiv::setDataToUpdate($serviceFromSiv, $identifiers);

            $imeiLog = data_get($service, 'imeiLog', null);
            $this->revertImei($toUpdate, $imeiLog);

            if (in_array(data_get($toUpdate, 'status'), self::DONT_NEED_CONTEST)) {
                $this->serviceService->pushLogService($service, ['contest' => $toUpdate]);
                return $this->serviceService->updateService($service, $toUpdate);
            } else {
                $response = $this->sivService->contest(data_get($identifiers, 'servico_id'), $userId);
                $this->serviceService->pushLogService($service, $response);
                $status        = $response['status'];
                $statusFromTao = AdaptStatusFromSiv::adapt($status);
                if ($statusFromTao) {
                    $update = ['status' => $statusFromTao];
                    $this->revertImei($update, $imeiLog);

                    return $this->serviceService->updateService($service, $update);
                } else {
                    logger('ClaroContest', ['siv' => $serviceFromSiv, 'status' => $statusFromTao]);
                }
            }
        }

        throw ClaroExceptions::CONTEST_INVALID_RESPONSE();
    }

    private function revertImei(array &$toUpdate, $imeiLog): void
    {
        if (! empty($imeiLog)) {
            $toUpdate['imei']    = $imeiLog;
            $toUpdate['imeiLog'] = '';
        }
    }
}
