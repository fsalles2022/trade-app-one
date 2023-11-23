<?php

namespace TradeAppOne\Domain\Services;

use Illuminate\Support\Facades\DB;
use TradeAppOne\Domain\Components\Helpers\ConstantHelper;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Factories\ContestFactory;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\AvailableService;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\ServicesServiceOption;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Domain\Repositories\Collections\ServiceRepository;
use TradeAppOne\Exceptions\BusinessExceptions\InvalidServiceStatus;
use TradeAppOne\Exceptions\BusinessExceptions\ServiceAlreadyInProgress;
use TradeAppOne\Exceptions\BusinessExceptions\ServiceNoExistException;
use TradeAppOne\Exceptions\BusinessExceptions\ServiceNotFoundException;
use TradeAppOne\Facades\UserPolicies;

class ServiceService extends BaseService
{
    /**
     * @var ServiceRepository
     */
    private $repository;
    private $saleRepository;

    public function __construct(
        ServiceRepository $repository,
        SaleRepository $saleRepository
    ) {
        $this->repository     = $repository;
        $this->saleRepository = $saleRepository;
    }

    /** @throws */
    public function contestService(array $payload)
    {
        $service = $this->saleService->findService($payload['serviceTransaction']);

        if (! $service instanceof Service) {
            throw new ServiceNotFoundException();
        }

        $invalidStatusToContest = [
            ServiceStatus::APPROVED,
            ServiceStatus::PENDING_SUBMISSION
        ];

        if (in_array($service->status, $invalidStatusToContest)) {
            throw new ServiceAlreadyInProgress($service->status);
        }

        $assistance = ContestFactory::make($service->operator);

        return $assistance->contestService($service, $payload);
    }

    public function editStatusByContext($serviceTransaction, $status)
    {
        $service     = $this->saleService->findService($serviceTransaction);
        $pointOfSale = $service->sale->pointOfSale;
        $user        = UserPolicies::getUser();

        throw_if((! $service instanceof Service), new ServiceNoExistException());

        $availableStates = ConstantHelper::getAllConstants(ServiceStatus::class);

        throw_if((! in_array($status, $availableStates)), new InvalidServiceStatus());

        UserPolicies::hasAuthorizationUnderPointOfSale(data_get($pointOfSale, 'cnpj', ''));

        $log = [
            'user'    => [
                'cpf'  => $user->cpf,
                'name' => $user->firstName
            ],
            'message' => "Alterado para {$status}",
        ];

        $this->saleService->pushLogService($service, $log);

        return $this->repository->updateService($service, ['status' => $status]);
    }

    public function update($serviceTransaction, $data)
    {
        $updateOnly['imei']  = data_get($data, 'imei');
        $updateOnly['iccid'] = data_get($data, 'iccid');
        $user                = UserPolicies::getUser();
        $log                 = [
            'user'    => [
                'cpf'  => $user->cpf,
                'name' => $user->firstName
            ],
            'message' => "Alterado para " . json_encode($updateOnly),
        ];

        $service = $this->saleService->findService($serviceTransaction);

        throw_if((! $service instanceof Service), new ServiceNoExistException());

        $this->saleService->pushLogService($service, $log);
        return $this->repository->updateService($service, array_filter($updateOnly));
    }

    public function updateAvailableServices(array $data): bool
    {
        if (empty(data_get($data, 'pointOfSaleId'))) {
            return $this->updateServicesKey(
                'networkId',
                data_get($data, 'networkId'),
                data_get($data, 'services')
            );
        }

        return $this->updateServicesKey(
            'pointOfSaleId',
            data_get($data, 'pointOfSaleId'),
            data_get($data, 'services')
        );
    }

    private function updateServicesKey(
        string $key,
        int $id,
        array $servicesList
    ): bool {
        $servicesListToCreate = $servicesList;

        $availableServices = AvailableService::where($key, $id)
            ->with('options')
            ->get();

        foreach ($availableServices as $availableService) {
            $servicesListToCreate = $this->removeServiceIsNotOnTheList(
                $availableService,
                $servicesListToCreate
            );
        }

        $this->createServiceByKey(
            $key,
            $id,
            $servicesListToCreate,
            $servicesList
        );

        return true;
    }

    private function removeServiceIsNotOnTheList(
        AvailableService $availableService,
        array $servicesList
    ): array {
        foreach ($servicesList as $key => $service) {
            if ((int) data_get($service, 'serviceId') == $availableService->serviceId) {
                unset($servicesList[$key]);
                return $servicesList;
            }
        }

        $availableService->forceDelete();

        return $servicesList;
    }

    private function createServiceByKey(
        string $key,
        int $id,
        array $servicesListToCreate,
        array $servicesList
    ): void {
        switch ($key) {
            case 'networkId':
                $nullKey = 'pointOfSaleId';
                break;
            case 'pointOfSaleId':
                $this->fillPointOfSaleServicesWithNetworkServices($id);
                $nullKey = 'networkId';
        }

        foreach ($servicesList as $service) {
            if ($this->hasInCreateList(data_get($service, 'serviceId'), $servicesListToCreate)) {
                $createData = [
                    $nullKey => null,
                    $key => $id,
                    'serviceId' => data_get($service, 'serviceId'),
                ];

                $availableService = AvailableService::create($createData);

                if (! empty(data_get($service, 'optionId'))) {
                    $availableService->options()->attach(data_get($service, 'optionId'));
                }

                continue;
            }

            $availableService = AvailableService::where($key, $id)
                ->where('serviceId', data_get($service, 'serviceId'))
                ->get()
                ->first();

            $this->updateServiceOption(data_get($service, 'optionId'), $availableService);
        }
    }

    private function fillPointOfSaleServicesWithNetworkServices(
        int $pointOfSaleId
    ): void {
        $pointOfSale = PointOfSale::where('id', $pointOfSaleId)
            ->get()
            ->first();

        $networkAvailableServices = AvailableService::where(
            'networkId',
            $pointOfSale->networkId
        )
        ->with('options')
        ->get();

        foreach ($networkAvailableServices as $service) {
            if (! $this->checkIfServiceExistsInPointOfSale($pointOfSaleId, $service)) {
                $createData = [
                    'networkId' => null,
                    'pointOfSaleId' => $pointOfSaleId,
                    'serviceId' => $service->serviceId,
                ];

                $availableService = AvailableService::create($createData);

                if (! empty(current($service->options))) {
                    $optionId = current(current($service->options))->id;
                    $availableService->options()->attach($optionId);
                }
            }
        }
    }

    private function checkIfServiceExistsInPointOfSale(
        int $pointOfSaleId,
        AvailableService $service
    ): bool {
        $pointOfSaleServices = AvailableService::where('pointOfSaleId', $pointOfSaleId)->get();

        foreach ($pointOfSaleServices as $pointOfSaleService) {
            if ((int) $pointOfSaleService->serviceId === (int) $service->serviceId) {
                return true;
            }
        }

        return false;
    }

    private function hasInCreateList(
        int $serviceId,
        array $servicesListToCreate
    ): bool {
        foreach ($servicesListToCreate as $service) {
            if ((int) data_get($service, 'serviceId') === $serviceId) {
                return true;
            }
        }

        return false;
    }

    private function updateServiceOption(
        int $optionId,
        AvailableService $availableService
    ): void {
        if (! empty($optionId)) {
            $serviceServiceOption = ServicesServiceOption::where('availableServiceId', $availableService->id)->withTrashed()->get()->first();

            if (! empty($serviceServiceOption->id)) {
                $serviceServiceOption->optionId = $optionId;
                $serviceServiceOption->update();
                return;
            }

            $availableService->options()->attach($optionId);
            return;
        }

        if (empty($optionId)) {
            DB::table('services_serviceOptions')
                ->where('availableServiceId', $availableService->id)
                ->delete();

            return;
        }
    }
}
