<?php

namespace Discount\Http\Requests;

use Discount\Enumerators\DiscountModes;
use Discount\Enumerators\DiscountStatus;
use Illuminate\Validation\Rule;
use TradeAppOne\Domain\Components\Helpers\ConstantHelper;
use TradeAppOne\Http\Requests\FormRequestAbstract;

class DiscountFormRequest extends FormRequestAbstract
{
    public const CREATE               = 'create';
    public const UPDATE               = 'update';
    public const DISCOUNTS            = 'discounts';
    public const DISCOUNT_IN_SALE     = 'discountsInSale';
    public const SIMULATION           = 'simulation';
    public const DISCOUNT_OR_REBATE   = 'discountsOrRebateInSale';
    public const SWITCH_STATUS_ACTION = 'switchStatusAction';
    public const CHANGE_DATES         = 'changeDatesAction';
    
    public function authorize(): bool
    {
        return true;
    }

    /** @return mixed[]|null */
    public function rules(): ?array
    {
        $action = $this->route()->getActionMethod();

        switch ($action) {
            case self::CREATE:
                return $this->onCreate();

            case self::UPDATE:
                return $this->onUpdate();

            case self::DISCOUNTS:
                return $this->onList();

            case self::DISCOUNT_IN_SALE:
                return $this->onDiscountsInSale();

            case self::SIMULATION:
                return $this->onSimulation();

            case self::DISCOUNT_OR_REBATE:
                return $this->discountOrRebate();

            case self::SWITCH_STATUS_ACTION:
                return $this->switchStatusAction();

            case self::CHANGE_DATES:
                return $this->changeDatesAction();

            default:
                return [];
        }
    }

    /** @return mixed[] */
    private function onCreate(): array
    {
        return [
            'title'      => 'required|string',
            'filterMode' => ['required', Rule::in(ConstantHelper::getAllConstants(DiscountModes::class))],
            'startAt'    => 'required|date|before_or_equal:endAt|after_or_equal:' . date('Y-m-d'),
            'endAt'      => 'required|date|after_or_equal:startAt',

            'products'              => 'required',
            'products.*.operator'   => 'required|string',
            'products.*.operations' => 'required|array',
            'products.*.plans'      => 'sometimes|array',
            'products.*.promotions' => 'sometimes|array',

            'promotions'             => 'sometimes',
            'promotions.*.product'   => 'required|string',
            'promotions.*.promotion' => 'nullable|string',

            'devices'            => 'required',
            'devices.*.ids'      => 'required|array|exists:devices_outsourced,id',
            'devices.*.discount' => 'required|numeric|min:1|max:99999999',

            'pointsOfSale' => 'required_if:filterMode,CHOSEN|array|exists:pointsOfSale,cnpj',
        ];
    }

    /** @return mixed[] */
    private function onUpdate(): array
    {
        return [
            'title'                => 'sometimes|required|string',
            'filterMode'           => ['sometimes', Rule::in(ConstantHelper::getAllConstants(DiscountModes::class))],
            'startAt'              => 'sometimes|date|before_or_equal:endAt',
            'endAt'                => 'sometimes|date|after_or_equal:startAt',
            'status'               => ['sometimes', Rule::in(ConstantHelper::getAllConstants(DiscountStatus::class))],

            'products'              => 'sometimes|required',
            'products.*.operator'   => 'sometimes|required|string',
            'products.*.operations' => 'sometimes|required|array',
            'products.*.plans'      => 'sometimes|nullable|sometimes|array',
            'products.*.promotions' => 'sometimes|nullable|sometimes|array',

            'promotions'             => 'sometimes',
            'promotions.*.product'   => 'required|string',
            'promotions.*.promotion' => 'nullable|string',

            'devices'             => 'sometimes|required',
            'devices.*.ids'       => 'sometimes|required|array',
            'devices.*.discount'  => 'sometimes|required|numeric|min:1|max:99999999',

            'pointsOfSale' => 'required_if:filterMode,CHOSEN|array|exists:pointsOfSale,cnpj',

            'idCampanha'     => 'sometimes|string',
            'ticketDiscount' => 'sometimes|string',
        ];
    }

    /** @return mixed[] */
    private function onList(): array
    {
        return [
            'title'       => 'sometimes|string',
            'operation'   => 'sometimes|string',
            'operator'    => 'sometimes|string',
            'product'     => 'nullable|sometimes|string',
            'status'      => ['sometimes', Rule::in(ConstantHelper::getAllConstants(DiscountStatus::class))],
            'devices'     => 'sometimes|array',
            'model'       => 'sometimes|string',
            'startAt'     => 'sometimes|date',
            'endAt'       => 'sometimes|date',
            'startDate'   => 'sometimes|date',
            'endDate'     => 'sometimes|date',
            'updatedAt'   => 'sometimes|date',
            'networks'    => 'sometimes|array'
        ];
    }

    /** @return string[] */
    private function onDiscountsInSale(): array
    {
        return [
            'operator'         => 'required|string',
            'operation'        => 'required|array',
            'deviceIdentifier' => 'sometimes|string'
        ];
    }

    /** @return string[] */
    private function onSimulation(): array
    {
        return [
            'deviceId' => 'required|exists:devices_discounts,deviceId',
        ];
    }

    /** @return string[] */
    private function discountOrRebate():array
    {
        return [
            'operator'         => 'required|string',
            'operations'       => 'required|array',
            'deviceIdentifier' => 'sometimes|string'
        ];
    }

    /** @return mixed[] */
    private function switchStatusAction(): array
    {
        return [
            'status' => ['required', Rule::in(ConstantHelper::getAllConstants(DiscountStatus::class))],
        ];
    }

    /** @return string[] */
    private function changeDatesAction(): array
    {
        return [
            'ids' => 'sometimes|array',
            'startAt' => 'required|date',
            'endAt' => 'required|date',
            'filters' => 'sometimes|array',
        ];
    }
}
