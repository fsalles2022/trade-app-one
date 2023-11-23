<?php

namespace TradeAppOne\Console\Commands;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\Device;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use Illuminate\Console\Command;

class RemoveDuplicatesDevices extends Command
{
    protected $signature = 'devices:remove-duplicates';

    public function handle()
    {
        $matchDevices = Device::query()
            ->withTrashed()
            ->selectRaw('label, count(*) as total')
            ->groupBy('label')
            ->having('total', '>', 1)
            ->get();

        $this->output->progressStart($matchDevices->count());

        $matchDevices->map(function ($match) {
            $this->output->progressAdvance();

            $devices      = $this->getDeviceByLabel($match->label);
            $deviceUnique = $devices->shift();

            $this->info(PHP_EOL . 'DeviceId - ' . $deviceUnique->id);
            $this->info(PHP_EOL . 'DeviceLabel - ' . $deviceUnique->label);
            $this->changeSalesDevicesId($devices, $deviceUnique);
            $this->removeDuplicates($devices);
        });

        $this->output->progressFinish();
    }

    private function removeDuplicates(Collection $devices)
    {
        $devices->map(function (Device $device) {
            $device->forceDelete();
        });
    }

    private function changeSalesDevicesId(Collection $devices, Device $deviceUnique)
    {
        $saleRepository = resolve(SaleRepository::class);
        $devicesId      = $devices->pluck('id')->toArray();
        $devicesLabel   = $devices->first()->label;

        $sales = $saleRepository
            ->createModel()
            ->where('services.operator', Operations::TRADE_IN_MOBILE)
            ->whereIn('services.device.id', $devicesId)
            ->where('services.device.label', $devicesLabel)
            ->get();

        foreach ($sales as $sale) {
            $services = $sale->services
                ->whereIn('device.id', $devicesId)
                ->where('device.label', $devicesLabel);

            foreach ($services as $service) {
                $this->info('ServiceTransaction - ' . $service->serviceTransaction);
                $saleRepository->updateService($service, ["device.id" => $deviceUnique->id]);
            }
        }
    }

    private function getDeviceByLabel(string $label)
    {
        return Device::query()
            ->withTrashed()
            ->where('label', $label)
            ->get();
    }
}
