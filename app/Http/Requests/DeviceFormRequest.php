<?php

namespace TradeAppOne\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeviceFormRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $action = $this->route()->getActionMethod();

        switch ($action) {
            case 'getDevicesPaginated':
                return $this->onDevicesPaginated();
                break;
            case 'getDeviceFilteredByType':
                return $this->onByTypes();
                break;
            default:
                return [];
        }
    }

    private function onDevicesPaginated()
    {
        return [
            'model'   => 'sometimes',
            'color'   => 'sometimes',
            'brand'   => 'sometimes',
            'storage' => 'sometimes',
            'label'   => 'sometimes'

        ];
    }

    private function onByTypes()
    {
        return [
            'type'   => 'required',
        ];
    }
}
