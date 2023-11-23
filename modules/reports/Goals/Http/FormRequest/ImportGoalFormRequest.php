<?php

namespace Reports\Goals\Http\FormRequest;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class ImportGoalFormRequest extends FormRequestAbstract
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'file'        => 'mimes:csv',
        ];
    }
}
