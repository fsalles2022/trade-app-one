<?php

namespace Uol\Models;

use TradeAppOne\Domain\Models\Collections\Service;

class UolCurso extends Service
{
    public function fill(array $attributes)
    {
        $this->fillable = array_merge(parent::getFillable(), ['passportType', 'passportSerie', 'passportNumber']);
        return parent::fill($attributes);
    }

    public function rules(): array
    {
        return [
            'passportNumber' => 'sometimes|required',
            'passportSerie' => 'sometimes|required',
            'passportType' => 'required',
            'gatewayReference' => 'sometimes|required',
            'customer.firstName' => 'required',
            'customer.lastName' => 'required',
            'customer.cpf' => 'required|cpf',
            'customer.mainPhone' => 'required',
            'customer.email' => 'required|email',
        ];
    }
}
