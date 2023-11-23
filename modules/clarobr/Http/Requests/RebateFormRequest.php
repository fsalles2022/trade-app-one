<?php

namespace ClaroBR\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class RebateFormRequest extends FormRequestAbstract
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'network'  => 'required',
            'from' => 'required_with:plan|without_undefined',
            'plan' => 'required_with:plan|without_undefined',
            'model' => 'required_with:plan|without_undefined',
            'areaCode' => 'required_with:plan|without_undefined',
        ];
    }
}
