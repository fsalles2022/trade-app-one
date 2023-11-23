<?php

namespace Movile\Models;

use TradeAppOne\Domain\Models\Collections\Service;

class MovileCubes extends Service
{
    public function fill(array $attributes)
    {
        $this->fillable = array_merge(parent::getFillable(), ['device', 'msisdn']);
        return parent::fill($attributes);
    }

    public function rules(): array
    {
        return [
            'device.price' => 'required|numeric|min:699',
            'device.imei'  => 'required|unique:mongodb.sales,services.device.imei',
            'msisdn'       => 'required',
            'customer'     => 'sometimes',
        ];
    }
}
