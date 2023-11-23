<?php

namespace Integrators\Services;

use Carbon\Carbon;
use Integrators\Adapters\ResidentialSaleAdapter;
use Integrators\Enumerators\Integrators;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Domain\Repositories\Collections\UserRepository;
use TradeAppOne\Exceptions\BusinessExceptions\SaleNotFoundException;
use TradeAppOne\Exceptions\BusinessExceptions\SaleObservationExceptions;
use TradeAppOne\Http\Resources\PointOfSaleResource;

class ResidentialSaleService
{
    protected $saleRepository;

    public function __construct(SaleRepository $saleRepository)
    {
        $this->saleRepository = $saleRepository;
    }

    public function handle($saleData): ?Sale
    {
        $sale = $this->saleRepository
            ->createModel()
            ->newQuery()
            ->with('services')
            ->where('services.operatorIdentifiers.venda_id', $saleData['id'])
            ->get()
            ->toBase();
        return $sale->isEmpty() ? $this->save($saleData) : null;
    }

    public function save($saleData): Sale
    {
        $sale = new Sale();
        $sale->setTransactionNumber();
        $saleAdapted = ResidentialSaleAdapter::adapt($saleData);

        $sale->fillable(array_merge(
            $sale->getFillable(),
            [Sale::CREATED_AT]
        ));

        $sale->fill($saleAdapted);
        $sale->pointOfSale = (new PointOfSaleResource())->map(data_get($saleAdapted, 'pointOfSale'));
        if ($sale->user) {
            $userFounded = resolve(UserRepository::class);
            $user        = $userFounded->find(data_get($sale->user, 'id'));
            if ($user !== null) {
                $userAuthAlternate   = $user->userAuthAlternate()->first();
                $sale->userAlternate = $userAuthAlternate ? $userAuthAlternate->toArray(): null;
            }
        }

        foreach ($saleAdapted['services'] as $index => $requestedService) {
            $service                     = (new Service())->forceFill($requestedService);
            $service->serviceTransaction = $sale->saleTransaction . '-' . $index;
            $sale->services()->associate($service);
        }

        return $this->saleRepository->save($sale);
    }

    public function update($saleData)
    {
        $sale = Sale::query()
            ->where('services.operatorIdentifiers.venda_id', $saleData['id'])
            ->get()->first();
        throw_if($sale === null, SaleNotFoundException::class);
        $saleAdapted = collect(ResidentialSaleAdapter::adapt($saleData));
        foreach (data_get($saleAdapted, 'services') as $toUpdate) {
            $serviceToUpdate         = $this->saleRepository->findByIntegratorId(
                data_get($toUpdate, 'operatorIdentifiers.venda_id'),
                data_get($toUpdate, 'operatorIdentifiers.servico_id'),
                Integrators::SIV
            );
            $toUpdate['logStatus'][] = array_filter([
                'status' => data_get($serviceToUpdate, 'status', ''),
                'statusThirdParty' => data_get($serviceToUpdate, 'statusThirdParty', ''),
                'updatedAt' => Carbon::now()->toDateTimeString()
            ]);
            $toUpdate['log']         = data_get($serviceToUpdate, 'log', []);

            if ($this->checkLogIsValid($toUpdate['log'], $saleData)) {
                $observation = $this->setObservation($saleData, $toUpdate);

                if ($observation) {
                    $toUpdate['log'][] = $observation;
                }
            }

            $logs = data_get($serviceToUpdate, 'logStatus', []);

            $toUpdate['logStatus'] = array_merge($toUpdate['logStatus'], $logs);
            if ($serviceToUpdate instanceof Service) {
                $this->saleRepository->updateService($serviceToUpdate, $toUpdate);
            }
        }
        return $sale->fresh();
    }

    /**
    * @param mixed[] $data
    * @return array
    */
    private function setObservation(array $data, array $operation): array
    {
        $checkIsTrue = $operation['operator'] === Operations::CLARO && in_array($operation['operation'], Operations::CLARO_RESIDENTIAL_STATUS_IMPORT);

        if (! $checkIsTrue) {
            return [];
        }

        return [
            'observation' => $data['observacao'],
            'observationId' => $data['observacaoId']
        ];
    }

    /**
     * @param mixed[] $logs
     * @param mixed[] $data
     */
    private function checkLogIsValid(array $logs, array $data): bool
    {
        foreach ($logs as $log) {
            $observationId = data_get($log, 'observationId');

            if ($observationId === $data['observacaoId']) {
                return false;
            }
        }

        return true;
    }
}
