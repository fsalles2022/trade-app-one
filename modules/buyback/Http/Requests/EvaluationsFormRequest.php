<?php

namespace Buyback\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class EvaluationsFormRequest extends FormRequestAbstract
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'label'   => 'sometimes|string|nullable',
            'network' => 'sometimes|array|nullable'
        ];
    }
}
