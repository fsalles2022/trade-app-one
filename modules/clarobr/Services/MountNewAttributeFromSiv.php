<?php

namespace ClaroBR\Services;

use ClaroBR\Connection\SivConnectionInterface;
use ClaroBR\Exceptions\AttributeNotFound;
use Discount\Services\DiscountService;
use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Services\MountNewAttributesService;

class MountNewAttributeFromSiv implements MountNewAttributesService
{
    protected $connection;
    private $discountService;

    public function __construct(SivConnectionInterface $sivConnection, DiscountService $discountService)
    {
        $this->connection      = $sivConnection;
        $this->discountService = $discountService;
    }

    public function getAttributes(array $service): array
    {
        $user       = Auth::user();
        $attributes = [];
        $mode       = data_get($service, 'mode');
        throw_if(empty($mode), new AttributeNotFound('mode'));
        switch ($mode) {
            case Modes::PORTABILITY:
                $attributes['areaCode'] = MsisdnHelper::getAreaCode($service['portedNumber']);
                break;
            case Modes::MIGRATION:
                $attributes['areaCode'] = MsisdnHelper::getAreaCode($service['msisdn']);
                break;
            case Modes::ACTIVATION:
                $attributes['areaCode'] = $service['areaCode'];
                break;
        }

        $plans = $this->connection->plans([
            'id'  => $service['product'],
            'ddd' => data_get($attributes, 'areaCode')
        ])->toArray();

        $product = (new ClaroBRMapSale())
            ->extractProductAttributes(
                $plans,
                $service['product'],
                $attributes['areaCode'],
                data_get($service, 'promotion')
            );

        if ($this->shouldCalcRebate($user, $service)) {
            $device               = data_get($service, 'device');
            $attributes['device'] = (new ClaroBRExtractRebate($this->connection))
                ->extract(
                    mb_strtolower($product->label),
                    $device,
                    $attributes['areaCode'],
                    $service['from']
                );
        }

        if ($originalDependents = data_get($service, 'dependents')) {
            $attributes['dependents'] = (new ClaroBRFillDependents($this->connection))
                ->fill($user->cpf, $originalDependents, $attributes['areaCode']);
        }
        return array_merge($attributes, $product->toArray());
    }

    private function shouldCalcRebate(User $user, array $service): bool
    {
        $operation = data_get($service, 'operation');
        $operator  = data_get($service, 'operator');

        if ($this->hasTriangulation($user, $operator, $operation)) {
            return false;
        }

        return $this->hasRebate($user, $service);
    }

    private function hasRebate(User $user, array $service): bool
    {
        $operation = array_wrap(data_get($service, 'operation', []));

        $networkHasRebate = ClaroBRDiscountService::shouldUseRebate($user, $operation);

        return $networkHasRebate
            && filled(data_get($service, 'device'))
            && filled(data_get($service, 'from'));
    }

    private function hasTriangulation(User $user, string $operator, string $operation): bool
    {
        return $this->discountService->triangulationsAvailable($user, [
            'operator' => $operator,
            'operation' => $operation
        ])->isNotEmpty();
    }
}
