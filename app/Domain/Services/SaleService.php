<?php

namespace TradeAppOne\Domain\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Integrators\Enumerators\Integrators;
use League\Csv\Writer;
use TradeAppOne\Domain\Enumerators\ImeiConstant;
use TradeAppOne\Domain\Enumerators\Importables;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Factories\SaleFactory;
use TradeAppOne\Domain\Factories\ServicesIntegrationResponseFactory;
use TradeAppOne\Domain\Importables\ImportableFactory;
use TradeAppOne\Domain\Importables\ImportEngine;
use TradeAppOne\Domain\Importables\OiResidentialSaleImportable;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Repositories\Collections\SalePaginatedRepository;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Domain\Services\NetworkHooks\NetworkHooksFactory;
use TradeAppOne\Domain\Services\Sale\ServiceOptionsFilter;
use TradeAppOne\Domain\Services\Update\SaleUpdateFactory;
use TradeAppOne\Events\PreAnalysisEvent;
use TradeAppOne\Exceptions\BusinessExceptions\ServiceAlreadyInProgress;
use TradeAppOne\Exceptions\BusinessExceptions\ServiceNotFoundException;
use TradeAppOne\Features\Customer\Adapter\CustomerSaleAdapter;
use Illuminate\Support\Collection as CollectionAlias;
use Tradehub\Services\TradeHubService;

class SaleService extends BaseService
{
    /** @var SaleRepository */
    private $repository;

    /** @var SalePaginatedRepository */
    private $salePaginatedRepository;

    /** @var TradeHubService */
    private $tradeHubService;

    public function __construct(
        SaleRepository $repository,
        SalePaginatedRepository $salePaginatedRepository,
        TradeHubService $tradeHubService
    ) {
        $this->repository              = $repository;
        $this->salePaginatedRepository = $salePaginatedRepository;
        $this->tradeHubService         = $tradeHubService;
    }

    /** @throws */
    public function new(string $captchaCode, string $captchaKey, string $source, User $user, $requestedServices, $pointOfSaleId)
    {
        if (((int) config('utils.captcha.isEnabled')) === 1) {
            $this->tradeHubService->validateCaptcha($captchaCode, $captchaKey);
        }

        event(new PreAnalysisEvent(new CustomerSaleAdapter($requestedServices)));

        $pointOfSale = $this->pointOfSaleService->checkPermissionAndReturnPointOfSale($user, $pointOfSaleId);
        $sale        = SaleFactory::make($source, $user, $pointOfSale, $requestedServices);
        return $this->repository->save($sale);
    }

    public function findService(string $serviceTransaction): ?Service
    {
        return $this->repository->findInSale($serviceTransaction);
    }

    public function filterBuyback($parameters)
    {
        return $this->repository->paginateBuyback($parameters, 10);
    }

    public function filter(array $parameters)
    {
        return $this->repository->paginate($parameters, 10);
    }

    /**
     * @param User $user
     * @param mixed[] $parameters
     * @param int|null $skip
     * @param int|null $take
     * @return Collection
     */
    public function filterAllActivationByContext(
        User $user,
        array $parameters,
        ?int $skip = null,
        ?int $take = null
    ): Collection {
        return $this->repository->filterAllActivationByContext(
            $user,
            $parameters,
            $skip,
            $take
        );
    }

    /**
     * @param User $user
     * @param mixed[] $parameters
     * @param int|null $skip
     * @param int|null $take
     * @return Collection
     */
    public function filterAllSecuritySystemsByContext(
        User $user,
        array $parameters,
        ?int $skip = null,
        ?int $take = null
    ): Collection {
        return $this->repository->filterAllSecuritySystemsByContext(
            $user,
            $parameters,
            $skip,
            $take
        );
    }

    /**
     * @param User $user
     * @param mixed[] $parameters
     * @param int|null $skip
     * @param int|null $take
     * @return Collection
     */
    public function filterAllBuybackByContext(
        User $user,
        array $parameters,
        ?int $skip = null,
        ?int $take = null
    ): Collection {
        return $this->repository->filterAllBuybackByContext(
            $user,
            $parameters,
            $skip,
            $take
        );
    }

    /**
     * @param mixed[] $parameters
     * @param int|null $skip
     * @param int|null $take
     * @return Collection
     */
    public function filterAll(
        array $parameters,
        ?int $skip = null,
        ?int $take = null
    ): Collection {
        return $this->repository->filterAll(
            $parameters,
            $skip,
            $take
        );
    }

    public function filterByContext(User $user, array $parameters, int $page, int $perPage = SalePaginatedRepository::QUANTITY_PER_PAGE)
    {
        return $this->salePaginatedRepository->searchByFilters($user, $parameters, $page, $perPage);
    }

    public function filterToIntegrators(User $user, array $parameters, $page)
    {
        return $this->salePaginatedRepository->searchByFiltersToIntegrators($user, $parameters, $page);
    }

    public function filterByBuyback(User $user, $parameters, int $page)
    {
        $operations = $user->getNetwork()->getTradeInMobileOperations();
        return $this->salePaginatedRepository->searchBuybackByFilters($operations, $parameters, $page);
    }

    public function getSubmittedSalesToSentinel(string $operator = '', $options = []): Collection
    {
        if (filled($operator)) {
            $builder = $this->repository
                ->where('services.operator', $operator)
                ->whereIn('services.status', [
                    ServiceStatus::ACCEPTED,
                    ServiceStatus::SUBMITTED
                ]);
            if ($initialDate = data_get($options, 'initialDate')) {
                $builder = $builder->where('createdAt', '>=', $initialDate);
            }
            if ($finalDate = data_get($options, 'finalDate')) {
                $builder = $builder->where('createdAt', '<=', $finalDate);
            }
            if ($limit = data_get($options, 'queryLimit')) {
                $builder = $builder->limit((int) $limit);
            }
            return $builder->get();
        }

        return $this->repository
            ->whereIn('services.status', [
                ServiceStatus::ACCEPTED
            ]);
    }

    /** @throws */
    public function integrateService(array $payload)
    {
        $service = $this->repository->findInSale($payload['serviceTransaction']);

        if (! $service instanceof Service) {
            throw new ServiceNotFoundException();
        }

        if (in_array($service->status, [ServiceStatus::APPROVED, ServiceStatus::ACCEPTED])) {
            throw new ServiceAlreadyInProgress($service->status);
        }

        if ($service->isPreSale && is_null($service->imei)) {
            $service->imei = ImeiConstant::DEFAULT;
        }

        $assistance = app()->make($service->operator);
        $response   = $assistance->integrateService($service, $payload);

        NetworkHooksFactory::run($service);

        return ServicesIntegrationResponseFactory::make($service, $response);
    }

    public function getByOperationId(string $field, array $values): Collection
    {
        return $this->repository->whereIn($field, $values);
    }

    public function updateStatusService(Service $service, string $status)
    {
        return $this->updateService($service, ['status' => $status]);
    }

    public function updateService(Service $service, array $attributes = []): Service
    {
        $instance = $service->serviceTransaction
            ? $this->findService($service->serviceTransaction)
            : $service;
        $instance->forceFill($attributes);
        return  $this->repository->updateService($instance, $attributes);
    }

    public function updateSale(Sale $instance, array $attributes = []): Sale
    {
        return $this->repository->updateSale($instance, $attributes);
    }

    public function findByImeiInValidSales(string $imei): ?Sale
    {
        return $this->repository->findByImeiAndStatus($imei, [ServiceStatus::APPROVED, ServiceStatus::ACCEPTED]);
    }

    public function pushLogService(Service $service, array $log)
    {
        $logs        = $service->log ?? [];
        $log['date'] = now()->toIso8601String();
        $logs[]      = $log;
        return $this->updateService($service, ['log' => $logs]);
    }

    public function getByNetworkSlug(string $networkSlug, $dates = []): Collection
    {
        $builder = $this->repository->createModel();
        if ($initialDate = data_get($dates, 'initialDate')) {
            $builder = $builder->where('createdAt', '>', $initialDate);
        }
        if ($finalDate = data_get($dates, 'finalDate')) {
            $builder = $builder->where('createdAt', '<', $finalDate);
        }
        return $builder->where('pointOfSale.network.slug', $networkSlug)->get();
    }

    public function saveBackOffice(string $serviceTransaction, User $user, array $data): ?Service
    {
        $service = $this->repository->findInSale($serviceTransaction);

        $data = array_merge($data, ['user' => $user->toMongoAggregation()]);

        $backOffices = $service->backoffice ?? [];

        $backOffices[] = $data;

        return $this->updateService($service, ['backoffice' => $backOffices]);
    }

    public function options(array $filters, User $user): ?array
    {
        return ServiceOptionsFilter::make($user, $filters)
            ->verifyCarteirizacao()
            ->verifyWithDevice()
            ->verifyIccidSearch()
            ->verifyStatusDisabledAutentica()
            ->filter();
    }

    public function findBySunId(string $saleId, string $serviceId)
    {
        $service = $this->repository->findByIntegratorId($saleId, $serviceId, Integrators::SUN);

        if ($service and $service->operator === Operations::VIVO) {
            return $service;
        }
    }

    public function updatePreSale(array $attributes = []): bool
    {
        $serviceTransaction = data_get($attributes, 'serviceTransaction');
        $imei               = data_get($attributes, 'imei');
        $saleService        = $this->findService($serviceTransaction);
        $isPreSale          = data_get($saleService, 'isPreSale', false);
        $networkSlug        = data_get($saleService->sale, 'pointOfSale.network.slug');

        $userNetworkSlug = Auth::user()->getNetwork()->slug;

        if ($saleService &&
            $isPreSale &&
            $imei &&
            ($userNetworkSlug === $networkSlug)
        ) {
            $updatedService   = $this->updateService($saleService, ['imei' => $imei]);
            $updateAssistance = SaleUpdateFactory::make($saleService->operator);
            $updateAssistance->update($updatedService->toArray());
            return true;
        }
        return false;
    }

    public function getModelCsv(): Writer
    {
        return OiResidentialSaleImportable::buildExample();
    }

    public function importResidentialSaleCsv(UploadedFile $uploadedFile): ?Writer
    {
        $importable = ImportableFactory::make(Importables::OI_RESIDENTIAL_SALE);
        $engine     = new ImportEngine($importable);
        return $engine->process($uploadedFile);
    }

    public function getSalesByCustomerCpf(string $cpf): CollectionAlias
    {
        return $this->repository->getSalesByCustomerCpf($cpf);
    }

    public function updateImei(Service $service, string $imei): Service
    {
        return $this->repository->updateImei($service, $imei);
    }
}
