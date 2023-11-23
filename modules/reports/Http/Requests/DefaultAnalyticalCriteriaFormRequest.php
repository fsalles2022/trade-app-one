<?php

namespace Reports\Http\Requests;

use Illuminate\Validation\Rule;
use TradeAppOne\Domain\Components\Helpers\ConstantHelper;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Http\Requests\FormRequestAbstract;

class DefaultAnalyticalCriteriaFormRequest extends FormRequestAbstract
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return mixed[] */
    public function rules(): array
    {
        return [
            'networks'     => 'array|nullable|exists:networks,slug',
            'pointsOfSale' => 'array|nullable|exists:pointsOfSale,cnpj',
            'status'       => ['array', 'nullable', Rule::in(ConstantHelper::getAllConstants(ServiceStatus::class))],
            'startDate'    => 'nullable|date',
            'endDate'      => 'nullable|date'
        ];
    }
}
