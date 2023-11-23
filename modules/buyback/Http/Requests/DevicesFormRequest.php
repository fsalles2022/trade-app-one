<?php

namespace Buyback\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class DevicesFormRequest extends FormRequestAbstract
{

    private const DEVICE     = 'devices';
    private const FIND_WATCH = 'findWatch';
    private const FIND_IPAD  = 'findIpad';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        switch ($this->route()->getActionMethod()) {
            case self::DEVICE:
                return $this->onDevices();
            case self::FIND_WATCH:
                return $this->onFindWatch();
            case self::FIND_IPAD:
                return $this->onFindIpad();
            default:
                return [];
        }
    }

    private function onDevices(): array
    {
        return [
            'model'   => 'sometimes',
            'brand'   => 'sometimes',
            'color'   => 'sometimes',
            'storage' => 'sometimes'
        ];
    }

    private function onFindWatch(): array
    {
        return [
            'serialNumber' => 'required|string'
        ];
    }

    private function onFindIpad(): array
    {
        return [
            'serialNumber' => 'required|string'
        ];
    }
}
