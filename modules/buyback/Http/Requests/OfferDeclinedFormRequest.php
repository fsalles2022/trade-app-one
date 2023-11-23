<?php

namespace Buyback\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class OfferDeclinedFormRequest extends FormRequestAbstract
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'customer'           => 'required',
            'customer.fullName'  => 'required',
            'customer.email'     => 'required_without:customer.mainPhone',
            'customer.mainPhone' => 'required_without:customer.email',
            'device'             => 'required',
            'device.id'          => 'required|numeric',
            'device.imei'        => 'required|digits:15',
            'questions'          => 'required',
            'questions.*.id'     => 'required|numeric',
            'questions.*.answer' => 'required',
            'reason'             => 'required',
            'operator'           => 'required',
            'operation'          => 'required'
        ];
    }
}
