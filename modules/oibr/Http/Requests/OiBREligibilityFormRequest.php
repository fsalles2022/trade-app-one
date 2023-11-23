<?php

namespace OiBR\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class OiBREligibilityFormRequest extends FormRequestAbstract
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'msisdn'   => 'sometimes|digits:11',
            'areaCode' => 'sometimes|required|digits:2',
        ];
    }
}
