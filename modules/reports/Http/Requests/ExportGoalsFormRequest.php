<?php

namespace Reports\Http\Requests;

use Illuminate\Validation\Rule;
use TradeAppOne\Http\Requests\FormRequestAbstract;

class ExportGoalsFormRequest extends FormRequestAbstract
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'months'         => ['array', 'nullable', Rule::in(range(1, 12))],
            'networks'       => 'array|nullable|exists:networks,slug',
            'pointsOfSale'   => 'array|nullable|exists:pointsOfSale,cnpj'
        ];
    }
}
