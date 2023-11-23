<?php

namespace TradeAppOne\Domain\Services;

use Illuminate\Database\Eloquent\Collection;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\AvailableService;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\ServiceOption;
use TradeAppOne\Domain\Repositories\Collections\PointOfSaleRepository;

class PointOfSaleIntegrationService
{
    private $repository;

    public function __construct(PointOfSaleRepository $repository)
    {
        $this->repository = $repository;
    }

    public function updateSivIntegration(array $request): array
    {
        $code      = data_get($request, 'codigo');
        $chipCombo = data_get($request, 'chip_combo');
        $cfLio     = data_get($request, 'cf_lio');
        $autentica = data_get($request, 'claro_autentica_promotor');

        $pointOfSale = PointOfSaleRepository::findByProviderIdentifiers($code)->first();
        $status      = [];

        filled($chipCombo) && $status[] = $this->updateChipCombo($pointOfSale, $chipCombo);
        filled($cfLio) && $status[]     = $this->updateCfLio($pointOfSale, $cfLio);
        filled($autentica) && $status[] = $this->autentica($pointOfSale, $autentica);

        return $status;
    }

    private function updateChipCombo(PointOfSale $pointOfSale, bool $status): array
    {
        $availableServices = AvailableService::findByPointOfSale($pointOfSale, [Operations::CLARO_PRE])->get();

        $option = ServiceOption::query()->firstOrCreate([
            'action' => ServiceOption::CLARO_PRE_CHIP_COMBO
        ]);

        return $this->attach($availableServices, $option, $status);
    }

    private function updateCfLio(PointOfSale $pointOfSale, bool $status): array
    {
        $availableServices = AvailableService::findByPointOfSale($pointOfSale, [Operations::CLARO_CONTROLE_FACIL])->get();

        $option = ServiceOption::query()->firstOrCreate([
            'action' => ServiceOption::CONTROLE_FACIL_LIO
        ]);

        return $this->attach($availableServices, $option, $status);
    }

    private function autentica(PointOfSale $pointOfSale, bool $status): array
    {
        $availableServices = AvailableService::findByPointOfSale($pointOfSale, [
            Operations::CLARO_CONTROLE_FACIL,
            Operations::CLARO_CONTROLE,
            Operations::CLARO_CONTROLE_BOLETO,
            Operations::CLARO_POS
        ])->get();

        $option = ServiceOption::query()->firstOrCreate([
            'action' => ServiceOption::AUTENTICA
        ]);

        return $this->attach($availableServices, $option, $status);
    }

    private function attach(Collection $availableServices, ServiceOption $option, bool $status): array
    {
        return $availableServices->map(static function (AvailableService $service) use ($status, $option) {
            $status
                ? $service->options()->syncWithoutDetaching([$option->id])
                : $service->options()->detach($option);

            return [
                $option->action => $service->options->contains($option),
                'service' => $service->service->operation
            ];
        })->toArray();
    }
}
