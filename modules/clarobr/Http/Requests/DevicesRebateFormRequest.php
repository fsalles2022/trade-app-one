<?php

namespace ClaroBR\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class DevicesRebateFormRequest extends FormRequestAbstract
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return ['device.model' => 'required'];
    }
}
