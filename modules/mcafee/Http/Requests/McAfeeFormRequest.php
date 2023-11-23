<?php

namespace McAfee\Http\Requests;

use Carbon\Carbon;
use Gateway\API\Brand;
use Illuminate\Validation\Rule;
use McAfee\Enumerators\McAfeePlans;
use TradeAppOne\Http\Requests\FormRequestAbstract;

class McAfeeFormRequest extends FormRequestAbstract
{
    private const PLANS  = 'plans';
    private const CANCEL = 'cancel';

    private const BY_INTERNET           = 'onByInternet';
    private const UPDATE_STATUS_PAYMENT = 'updateStatusPayment';

    public function authorize(): bool
    {
        return true;
    }

    /** @return array|string[] */
    public function rules(): array
    {
        switch (request()->route()->getActionMethod()) {
            case self::PLANS:
                return $this->onPlans();
                break;
            case self::CANCEL:
                return $this->onCancel();
                break;
            case self::BY_INTERNET:
                return $this->onByInternet();
                break;
            case self::UPDATE_STATUS_PAYMENT:
                return $this->updateStatusPayment();
                break;
            default:
                return [];
        }
    }

    /** @return string[] */
    private function onPlans(): array
    {
        return [
            'operation' => 'sometimes|string'
        ];
    }

    /** @return string[] */
    private function onCancel(): array
    {
        return [
            'serviceTransaction' => 'required'
        ];
    }

    /** @return string[] */
    private function onByInternet(): array
    {
        return [
            'captcha' => 'required',

            'service'          => 'required|array',
            'service.product' => ['required', Rule::in(McAfeePlans::available())],

            'service.customer'     => 'required|array',
            'service.customer.cpf' => 'required|cpf',
            'service.customer.firstName' => 'required|string',
            'service.customer.lastName'  => 'required|string',
            'service.customer.email'     => 'required|string',
            'service.customer.mainPhone' => 'required|string',
            'service.customer.password'  => 'required|string',

            'creditCard'            => 'required|array',
            'creditCard.name'       => 'required|string|max:20',
            'creditCard.flag'       => ['required', 'string', Rule::in(array_values(Brand::getConstants()))],
            'creditCard.pan'        => 'required|string|min:16|max:18',
            'creditCard.month'      => 'required|date_format:"m"',
            'creditCard.year'       => 'required|date_format:"y"|after_or_equal:' . Carbon::now()->format('y'),
            'creditCard.cvv'        => 'required|string|size:3'
        ];
    }

    /** @return string[] */
    private function updateStatusPayment(): array
    {
        return [
            'create_time' => 'required|string',
            'status' => 'required|string'
        ];
    }
}
