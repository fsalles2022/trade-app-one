<?php

namespace TradeAppOne\Http\Requests;

class SaleFormRequest extends FormRequestAbstract
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'captcha' => 'required',
            'pointOfSale' => 'required',
            'associateUserId' => 'sometimes',
            'services' => 'required',
            'services.*.operator' => ['required', 'hasOperatorPermission'],
            'services.*.operation' => 'required',
            'services.*.mode' => 'required',
            'services.*.product' => 'sometimes',
            'services.*.customer' => 'sometimes|required',
            'services.*.isPreSale' => 'sometimes|required|boolean',
            'services.*.customer.firstName' => 'sometimes|required',
            'services.*.customer.lastName' => 'sometimes|required',
            'services.*.recommendation' => 'sometimes',
            'services.*.hasRecommendation' => 'sometimes|boolean',
            'services.*.remoteSale' => 'sometimes|boolean',
            'services.*.urlOrigin' => 'sometimes|boolean',
            'service.*.term' => 'sometimes|array'
        ];
    }
}
