<?php

namespace Discount\Services;

use Discount\Repositories\DiscountRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Services\Sale\RequirementsForSale;

class BuildTriangulationToSale implements RequirementsForSale
{
    /** @var DiscountRepository */
    private $discountRepository;

    /** @var DiscountTimService */
    private $discountTimService;

    public function __construct(DiscountRepository $discountRepository, DiscountTimService $discountTimService)
    {
        $this->discountRepository = $discountRepository;
        $this->discountTimService = $discountTimService;
    }

    public function apply(array $service): array
    {
        if ($this->triangulationNotAvailable($service)) {
            return $service;
        }

        $deviceId   = Arr::get($service, 'device.id');
        $discountId = Arr::get($service, 'discount.id');

        // Rebate flow by TIM
        if ($this->discountTimService->shouldUseDiscountByOperation(Auth::user(), (string) data_get($service, 'operation'))) {
            $discountId = Arr::get($service, 'discount.discountId');
            $discount   = $this->discountTimService->getDiscountById($discountId);
            $device     = $discount->device;
            $product    = $discount->product;

            return array_merge($service, [
                'device' => [
                    'id'                    => $device->id,
                    'label'                 => $device->label,
                    'model'                 => $device->model,
                    'brand'                 => $device->brand,
                    'externalIdentifier'    => $device->externalIdentifier,
                    'discount'              => $discount->discount,
                    'priceWithout'          => (float) $device->price,
                    'priceWith'             => ((float) $device->price - (float) $discount->discount),
                ],
                'discount' => [
                    'id'                => $discount->id,
                    'discount'          => $discount->discount,
                    'discountProductId' => $discount->discountProductId,
                    'productlabel'      => $product->label,
                ]
            ]);
        }

        $discount   = $this->discountRepository->findByNetworkAndId(Auth::user()->getNetwork(), $discountId);
        $device     = $discount->devices->where('device.id', $deviceId)->first();
        $deviceOnly = array_filter($device->device->toArray());

        unset($deviceOnly['price']);

        $discountDevice = [
            'priceWithout' => (float) data_get($device, 'device.price'),
            'priceWith'    => (float) data_get($device, 'device.price') - (float) data_get($device, 'discount'),
            'discount'     => data_get($device, 'discount')
        ];

        return array_merge($service, [
            'device'   => array_merge($deviceOnly, $discountDevice),
            'discount' => array_filter([
                'id'         => $discount['id'],
                'title'      => $discount['title'],
                'discount'   => $device->discount,
                'idCampaign' => Arr::get($service, 'discount.idCampaign'),
                'coupon'     => Arr::get($service, 'discount.coupon')
            ])
        ]);
    }

    private function triangulationNotAvailable(array $service): bool
    {
        return ! ($this->hasDevice($service) && $this->hasTriangulation($service) && $this->isTelecommunication($service));
    }

    private function hasDevice(array $service): bool
    {
        return isset($service['device']);
    }

    private function isTelecommunication(array $service): bool
    {
        $operator           = data_get($service, 'operator');
        $telecommunications = array_keys(Operations::TELECOMMUNICATION_OPERATORS);

        return in_array($operator, $telecommunications, true);
    }

    private function hasTriangulation(array $service): bool
    {
        return isset($service['discount']);
    }
}
