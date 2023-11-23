<?php

namespace Discount\Services;

use ClaroBR\Services\ClaroBRDiscountService;
use ClaroBR\Services\SivService;
use Discount\Adapters\DiscountAdapter;
use Discount\Enumerators\DiscountModes;
use Discount\Exceptions\DiscountExceptions;
use Discount\Http\Resources\DiscountInSaleResource;
use Discount\Models\Discount;
use Discount\Repositories\DiscountRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Outsourced\Assistance\OutsourcedFactory;
use Outsourced\Crafts\Devices\OutsourcedDeviceDTO;
use Outsourced\Enums\Crafts;
use stdClass;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Policies\Authorizations;

class DiscountService
{
    /** @var DiscountRepository */
    private $discountRepository;

    /** @var DiscountTimService */
    private $discountTimService;

    /** @var Authorizations */
    private $authorizations;

    /** @var ClaroBRDiscountService */
    private $claroDiscountService;

    /** @var SivService */
    private $sivService;

    public function __construct(
        DiscountRepository $discountRepository,
        DiscountTimService $discountTimService,
        ClaroBRDiscountService $claroDiscountService,
        SivService $sivService,
        Authorizations $authorizations
    ) {
        $this->discountRepository   = $discountRepository;
        $this->discountTimService   = $discountTimService;
        $this->authorizations       = $authorizations;
        $this->claroDiscountService = $claroDiscountService;
        $this->sivService           = $sivService;
    }

    public function filter(User $user, array $filters = []): Builder
    {
        return $this->discountRepository
            ->filter($user, $this->adaptDate($filters));
    }

    /**
     * @param mixed[] $filters
     * @return mixed[]
     */
    private function adaptDate(array $filters): array
    {
        if (isset($filters['startDate']) && isset($filters['endDate'])) {
            $filters['startAt'] = $filters['startDate'] ?? null;
            $filters['endAt']   = $filters['endDate'] ?? null;
        }

        return $filters;
    }

    public function getDiscountById(int $id): Collection
    {
        $discount = $this->discountRepository->findById($id);
        return DiscountAdapter::adapt($discount);
    }

    public function triangulationInSale(User $user, array $filters): stdClass
    {
        $device           = data_get($filters, 'deviceIdentifier');
        $deviceOutsourced = $device ? $this->getDeviceOutsourced($user, $device) : null;

        $hasDeviceFromIntegration = OutsourcedFactory::hasIntegration($user->getNetwork()->slug, Crafts::DEVICES);

        if (empty($device) || $deviceOutsourced === null) {
            $triangulations = $this->triangulationsAvailable($user, $filters);
            return $this->dataReturnInSale($triangulations, Response::HTTP_OK, false, $hasDeviceFromIntegration);
        }

        if (empty($deviceOutsourced->sku)) {
            return $this->dataReturnInSale(collect(), Response::HTTP_UNPROCESSABLE_ENTITY, false, true);
        }

        $filters       += ['sku' => $deviceOutsourced->sku];
        $triangulations = $this->triangulationsAvailable($user, $filters);

        if ($triangulations->isEmpty()) {
            return $this->dataReturnInSale(collect(), Response::HTTP_NOT_ACCEPTABLE, false, true);
        }

        $triangulation = $triangulations->first();
        $devices       = $triangulation->devices;

        $filtered = $devices->filter(static function ($deviceDiscount) use ($deviceOutsourced) {
            return ((string) $deviceDiscount->device->sku === (string) $deviceOutsourced->sku);
        });

        $triangulations->first()->setRelation('devices', $filtered);

        return $this->dataReturnInSale(
            $triangulations,
            Response::HTTP_OK,
            true,
            true
        );
    }

    public function getDeviceOutsourced(User $user, $deviceIdentifier): ?OutsourcedDeviceDTO
    {
        $network    = $user->getNetwork()->slug;
        $outsourced = OutsourcedFactory::make($network, Crafts::DEVICES, false);

        return $outsourced
            ? $outsourced->findDevice($deviceIdentifier)
            : null;
    }

    public function triangulationsAvailableToPointOfSale(User $user, array $filters): Collection
    {
        $pointOfSaleIds = $user->pointsOfSale->pluck('id')->toArray();
        $discounts      = $this->discountRepository->discountsAvailable($user->getNetwork(), $filters);

        return $discounts->filter(static function (Discount $discount) use ($pointOfSaleIds) {
            return $discount->filterMode == DiscountModes::ALL || $discount->pointsOfSale->whereIn('id', $pointOfSaleIds)->isNotEmpty();
        });
    }

    public function triangulationsAvailable(User $user, array $filters): Collection
    {
        $pointOfSaleIds = $this->authorizations
            ->setUser($user)
            ->getPointsOfSaleAuthorized()
            ->pluck('id')
            ->toArray();

        $discounts = $this->discountRepository->discountsAvailable($user->getNetwork(), $filters);

        return $discounts->filter(static function (Discount $discount) use ($pointOfSaleIds) {
            return ($discount->filterMode == DiscountModes::ALL)
                ? true
                : $discount->pointsOfSale->whereIn('id', $pointOfSaleIds)->isNotEmpty();
        });
    }

    private function dataReturnInSale($data, int $status, bool $setDevice = false, bool $hasIntegration = false): stdClass
    {
        $return                 = new stdClass();
        $return->triangulations = $data;
        $return->setDevice      = $setDevice;
        $return->status         = $status;
        $return->hasIntegration = $hasIntegration;

        return $return;
    }

    public function getDiscountOrRebate(User $user, array $filters):array
    {
        $operations = data_get($filters, 'operation');

        if ($this->discountTimService->shouldUseDiscountByOperation($user, $operations[0] ?? [])) {
            return [
                'rebate' => [],
                'triangulations' => [
                    'triangulations' => $this->discountTimService->getDiscounts(),
                    'hasIntegration' => false,
                    'setDevice'      => false,
                ],
            ];
        }

        $receivedTriangulations = $this->triangulationInSale($user, $filters);

        if ($receivedTriangulations->status !== Response::HTTP_OK) {
            throw DiscountExceptions::failFetchingTriangulation($receivedTriangulations->status);
        }

        if ($receivedTriangulations->triangulations->isNotEmpty()) {
            return [
                'rebate'         => [],
                'triangulations' => DiscountInSaleResource::toArray(
                    $receivedTriangulations->triangulations,
                    $receivedTriangulations->setDevice,
                    $receivedTriangulations->hasIntegration,
                    $filters
                )
            ];
        }

        if (ClaroBRDiscountService::shouldUseRebate($user, $operations)) {
            return [
                'rebate'         => $this->getRebate($user),
                'triangulations' => []
            ];
        }

        return [
            'rebate'         => [],
            'triangulations' => DiscountInSaleResource::toArray(
                $receivedTriangulations->triangulations,
                $receivedTriangulations->setDevice,
                $receivedTriangulations->hasIntegration,
                $filters
            )
        ];
    }

    public function getRebate(User $user): array
    {
        $response    = $this->sivService->rebate(['network' => $user->getNetwork()->slug], $user);
        $rebateItems = collect(data_get($response, 'data.rebate', []));

        $rebateItems->transform(function ($item) use ($user) {
            $model = data_get($item, 'model');

            return [
                '_id' => data_get($item, '_id'),
                'model' => data_get($item, 'model'),
                'manufacturer' => data_get($item, 'manufacturer'),
                'sanitized' => data_get($item, 'sanitized'),
                'isPreSale' => $this->deviceIsPreSale($model, $user)
            ];
        });

        data_set($response, 'data.rebate', $rebateItems);
        return $response;
    }

    public function deviceIsPreSale(string $model, User $user): bool
    {
        $isPreSale = $user
            ->getNetwork()
            ->devices()
            ->where('label', 'like', "%$model%")
            ->first();

        return $isPreSale->pivot->isPreSale ?? 0;
    }
}
