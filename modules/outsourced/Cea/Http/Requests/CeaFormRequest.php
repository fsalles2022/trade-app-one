<?php

namespace Outsourced\Cea\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class CeaFormRequest extends FormRequestAbstract
{
    const IMPORT_GIFT_CARDS  = 'importGiftCards';
    const ACTIVATE_GIFT_CARD = 'activateGiftCard';

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $action = $this->route()->getActionMethod();
        switch ($action) {
            case self::IMPORT_GIFT_CARDS:
                return $this->onImport();
            case self::ACTIVATE_GIFT_CARD:
                return $this->onActivate();
            default:
                return [];
        }
    }

    private function onImport()
    {
        return [
            'file' => 'required|mimes:txt'
        ];
    }

    private function onActivate()
    {
        return [
            'cardNumber'         => 'required|string',
            'value'              => 'required|numeric',
            'partner'            => 'required|numeric',
            'customer.cpf'       => 'required|cpf',
            'customer.firstName' => 'required|string',
            'customer.lastName'  => 'required|string'
        ];
    }
}
