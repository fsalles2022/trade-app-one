<?php

namespace ClaroBR\Services\UpdateAttributes;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Services\SaleService;

class ClaroBRUpdateDeviceService implements ClaroBRUpdateAttributes
{
    protected $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    public function update(array $options = []): Collection
    {
        $networks = data_get($options, 'network');
        if ($networks && is_array($networks)) {
            foreach ($networks as $network) {
                $sales = $this->saleService->getByNetworkSlug($network);
                return $this->filterSalesWithDevice($sales);
            }
        } elseif ($serviceTransaction = data_get($options, 'serviceTransaction')) {
            $pickedService = $this->saleService->findService($serviceTransaction);
            return collect([$this->execute($pickedService)]);
        }
    }

    private function filterSalesWithDevice(Collection $sales): Collection
    {
        $c = new Collection();
        foreach ($sales as $sale) {
            $servicesWithDependent = $sale->services
                ->where('operator', Operations::CLARO)
                ->where('device', '!=', null);
            foreach ($servicesWithDependent as $serviceWithDevice) {
                $serviceUpdated = $this->execute($serviceWithDevice);
                $c->push($serviceUpdated);
            }
        }
        return $c;
    }

    private function execute(Service $serviceWithDevice)
    {
        if (! data_get($serviceWithDevice, 'device.model.label')) {
            $device          = data_get($serviceWithDevice, 'device');
            $label           = mb_strtoupper(str_replace('_', ' ', $device['model']));
            $device['label'] = $label;
            return $this->saleService->updateService($serviceWithDevice, ['device' => $device]);
        }
        return $serviceWithDevice;
    }
}
