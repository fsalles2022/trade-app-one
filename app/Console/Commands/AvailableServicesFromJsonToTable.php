<?php

namespace TradeAppOne\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\Service;

class AvailableServicesFromJsonToTable extends Command
{
    protected $signature   = 'availableServices:transport';
    protected $description = 'Moves AvailableServices from JSON to new table';

    public function handle()
    {
        $this->exctractServices(PointOfSale::all());
        $this->exctractServices(Network::all());
    }

    private function exctractServices(Collection $collection)
    {
        $this->info('======= Registering ========');
        $bar = $this->output->createProgressBar(count($collection));
        $bar->start();
        foreach ($collection as $model) {
            if (filled($model->availableServices)) {
                $servicesIds = $this->breakAvailableServices($model->availableServices);
                $services    = Service::query()->whereIn('id', $servicesIds)->get();
                $model->services()->saveMany($services);
            }
            $bar->advance();
        }
        $bar->finish();
        $this->info("\n============= DONE ===============\n");
    }

    private function breakAvailableServices(array $availableServices): array
    {
        $servicesBreakdown = [];
        foreach ($availableServices as $sector => $sectorValues) {
            foreach ($sectorValues as $operator => $operatorValues) {
                foreach ($operatorValues as $operation) {
                    $servicesBreakdown[] =$this->getIdForService($sector, $operator, $operation);
                }
            }
        }
        return $servicesBreakdown;
    }

    private function getIdForService($sector, $operator, $operation): int
    {
        return Service::firstOrCreate(['sector' => $sector, 'operator' => $operator, 'operation' => $operation])->id;
    }
}
