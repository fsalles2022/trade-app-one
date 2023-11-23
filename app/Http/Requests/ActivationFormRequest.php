<?php

namespace TradeAppOne\Http\Requests;

use Illuminate\Validation\Rule;
use TradeAppOne\Domain\Enumerators\ConfirmOperationStatus;

class ActivationFormRequest extends FormRequestAbstract
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'serviceTransaction' => 'required',
            'status'             => ['sometimes', 'required', Rule::in(ConfirmOperationStatus::STATUS)],
            'creditCard'         => 'array|sometimes'
        ];
    }
}
