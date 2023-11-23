<?php

namespace OiBR\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class OiBRPlansFormRequest extends FormRequestAbstract
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'pointOfSale' => 'required',
            'areaCode'    => 'required',
            'paymentType' => 'required',
        ];
    }
}
