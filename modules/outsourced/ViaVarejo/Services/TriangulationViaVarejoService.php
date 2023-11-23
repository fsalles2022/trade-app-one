<?php

namespace Outsourced\ViaVarejo\Services;

use Discount\Repositories\DiscountRepository;
use Illuminate\Support\Facades\Auth;
use Outsourced\Crafts\Triangulations\TriangulationsActionsInterface;
use Outsourced\ViaVarejo\Exceptions\ViaVarejoExceptions;
use Outsourced\ViaVarejo\Models\ViaVarejoCoupon;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\User;
use Outsourced\ViaVarejo\Helpers\UserCacheHelper;

class TriangulationViaVarejoService implements TriangulationsActionsInterface
{
    private $discountRepository;
    private const CASAS_BAHIA = 'CB';
    private const PONTO_FRIO  = 'PF';

    public function __construct(DiscountRepository $discountRepository)
    {
        $this->discountRepository = $discountRepository;
    }

    public function processCustomDataFromTriangulation(array $attributes): void
    {
        $coupon     = data_get($attributes, 'ticketDiscount');
        $campaign   = data_get($attributes, 'idCampanha');
        $discountId = data_get($attributes, 'discount.id');

        ViaVarejoCoupon::create([
            'coupon' => $coupon,
            'campaign' => $campaign,
            'discountId' => $discountId
        ]);
    }

    /** @param mixed[] $attributes */
    public function processUpdateFromTriangulation(array $attributes): void
    {
        $coupon     = data_get($attributes, 'ticketDiscount');
        $campaign   = data_get($attributes, 'idCampanha');
        $discountId = data_get($attributes, 'discount.id');

        $couponFromDiscount = ViaVarejoCoupon::where('discountId', $discountId)->first();

        if ($couponFromDiscount === null) {
            return;
        }

        ViaVarejoCoupon::where('discountId', $discountId)->update([
            'coupon'    => $coupon,
            'campaign'  => $campaign,
        ]);
    }

    /** @param mixed[] $requestParams */
    public function getCoupon(array $requestParams = []): ?ViaVarejoCoupon
    {
        $user        = Auth::user();
        $networkUser = $user->getNetwork() ?? new Network();

        $pointOfSaleUser = $this->getPointOfSaleCodeByUser($user);

        $abbreviationCompany = $this->getAbbreviationFromPointOfSale($pointOfSaleUser);

        $device    = data_get($requestParams, 'sku', null);
        $plan      = data_get($requestParams, 'plan', null);
        $promotion = data_get($requestParams, 'promotion', null);
        $operator  = data_get($requestParams, 'operator', null);
        $operation = data_get($requestParams, 'operation', null);

        $customParamsToQuery = $operator === Operations::TIM ?
                [ 'sku' => $device , 'operation' => $operation, 'operator' => $operator]
            :
                [ 'sku' => $device, 'product' => $plan];

        if ($promotion && $promotion !== 'undefined') {
            data_set($customParamsToQuery, 'promotion', $promotion);
        }

        $availableDiscounts = $this->discountRepository->discountsAvailable($networkUser, $customParamsToQuery);

        // Necessary to work with controle facil plans without promotions
        if ($availableDiscounts->isEmpty() && $operation === Operations::CLARO_CONTROLE_FACIL) {
            unset($customParamsToQuery['promotion']);

            $availableDiscounts = $this->discountRepository->discountsAvailable($networkUser, $customParamsToQuery);
        }

        $discountFound = $availableDiscounts->filter(static function ($discount) use ($abbreviationCompany) {
            $couponFromDiscount = ViaVarejoCoupon::where('discountId', $discount->id)
                ->first();

            if (str_contains($couponFromDiscount->coupon, $abbreviationCompany)) {
                return $discount;
            }
        })->first();
        $discountFound = data_get($discountFound, 'id', null);

        $coupon = ViaVarejoCoupon::where('discountId', $discountFound)->first();
        throw_if($coupon === null, ViaVarejoExceptions::couponNotFound());

        return $coupon;
    }

    private function getPointOfSaleCodeByUser(User $user): ?string
    {
        $pointOfSaleCode = UserCacheHelper::make()->getViaVarejoUserPointOfSaleAlternateByUserId($user->id);

        return $pointOfSaleCode ?: $user->pointsOfSale->first()->slug;
    }

    private function getAbbreviationFromPointOfSale($pointOfSaleUser): string
    {
        $pointOfSale = (int) $pointOfSaleUser;
        if (($pointOfSale >= 0 && $pointOfSale <= 999) || ($pointOfSale >= 4000)) {
            return self::PONTO_FRIO;
        }
        return self::CASAS_BAHIA;
    }
}
