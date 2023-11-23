<?php

namespace Buyback\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class RevaluationFormRequest extends FormRequestAbstract
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'serviceTransaction' => 'required',
            'evaluationType'     => 'sometimes|required|string',
            'questions'          => 'required',
            'questions.*.id'     => 'required|numeric',
            'questions.*.answer' => 'required'
        ];
    }
}
