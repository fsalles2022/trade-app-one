<?php

namespace TradeAppOne\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;

class RemapSalesWithTriangulationCommand extends Command
{
    const DONE = 'DONE';
    const FAIL = 'FAIL';
    const LOG  = 'triangulationRemap';

    protected $signature   = 'sales:remap-triangulation';
    protected $description = 'Remap Sales With Triangulations';

    public function handle(InputCommand $input)
    {
        $sales = $this->getSales();
        $count = $sales->count();

        if ($input->confirmSaleQuantity($this, $count)) {
            $this->output->progressStart($count);

            foreach ($sales as $sale) {
                $this->output->progressAdvance();
                $services = $this->filterServices($sale->services);

                foreach ($services as $service) {
                    $adapted = $this->adapter($service);
                    if ($this->validate($service, $adapted)) {
                        $this->update($service, $adapted);
                        $sale->touch();
                    }
                }
            }
            $this->output->progressFinish();
        }

        $this->info("\n==== Process Complete =====");
    }

    private function adapter(Service $service): array
    {
        $device   = data_get($service, 'device');
        $discount = data_get($device, 'discount');

        unset($device['discount'], $device['products'], $discount['products']);

        $newDevice = $this->buildDiscountDevice($device, $discount);

        return [
            'device' => $newDevice,
            'discount' => $discount,
            'log' => $this->getLog($service, self::DONE)
        ];
    }

    private function update(Service $service, array $attributes)
    {
        $service->forceFill($attributes);
        $service->touch();
    }

    private function buildDiscountDevice(array $device, $discount): array
    {
        $priceTotal    = (float) data_get($discount, 'price');
        $priceDiscount = (float) data_get($discount, 'discount');

        return array_merge($device, [
            'priceWithout' => $priceTotal,
            'priceWith'    => $priceTotal - $priceDiscount,
            'discount'     => $priceDiscount
        ]);
    }

    private function getSales(): Collection
    {
        return Sale::query()
            ->where('services.operator', '=', Operations::CLARO)
            ->where('services.device.discount.id', 'exists', true)
            ->where('services.discount', 'exists', false)
            ->get();
    }

    private function filterServices(Collection $services)
    {
        return $services->where('operator', '=', Operations::CLARO)
            ->filter(static function (Service $service) {
                return isset($service['device']['discount']['id'])
                    && empty($service['discount'])
                    && ($service['operator'] === Operations::CLARO );
            });
    }

    private function validate(Service $service, array $attributes): bool
    {
        $rules = [
            'device.priceWithout' => 'required|numeric',
            'device.priceWith'    => 'required|numeric',
            'device.discount'     => 'required|numeric',
            'device.id'           => 'required|numeric',
            'device.label'        => 'required|string',
            'device.sku'          => 'required|string',

            'discount.id'         => 'required|numeric',
            'discount.price'      => 'required|numeric',
            'discount.title'      => 'required|string',
            'discount.discount'   => 'required|numeric',
        ];

        $validator = Validator::make($attributes, $rules);

        if ($validator->fails()) {
            $this->update($service, ['log' => $this->getLog($service, self::FAIL)]);
            $this->info("\nErro: " . $validator->errors() . PHP_EOL . 'Service: ' . $service->serviceTransaction);
            return false;
        }

        return true;
    }

    private function getLog(Service $service, string $status): array
    {
        $logs   = $service->log ?? [];
        $logs[] = [self::LOG => $status];
        return $logs;
    }
}
