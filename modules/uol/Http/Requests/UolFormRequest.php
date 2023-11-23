<?php

namespace Uol\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class UolFormRequest extends FormRequestAbstract
{
    const CANCEL = 'cancel';

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $action = request()->route()->getActionMethod();

        switch ($action) {
            case self::CANCEL:
                return $this->onCancel();
            default:
                return [];
        }
    }

    public function onCancel()
    {
        return [
            'serviceTransaction' => 'required',
        ];
    }
}
