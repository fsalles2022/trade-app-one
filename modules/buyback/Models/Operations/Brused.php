<?php

namespace Buyback\Models\Operations;

use Illuminate\Validation\Rule;
use TradeAppOne\Domain\Enumerators\Formats;
use TradeAppOne\Domain\Models\Collections\Service;

class Brused extends Service
{
    public function fill(array $attributes)
    {
        $this->fillable = array_merge(
            parent::getFillable(),
            ['device', 'evaluations', 'evaluationsValues']
        );
        return parent::fill($attributes);
    }

    public function rules(): array
    {
        return [
            'device.id'                                 => 'required',
            'device.imei'                               => 'required|digits:15',
            'device.model'                              => 'required',
            'device.storage'                            => 'required',
            'device.color'                              => 'required',
            'evaluations.salesman.price'                => 'required',
            'evaluations.salesman.deviceNote'           => 'required',
            'evaluations.salesman.questions.*.id'       => 'required',
            'evaluations.salesman.questions.*.question' => 'required',
            'evaluations.salesman.questions.*.weight'   => 'required',
            'evaluations.salesman.questions.*.answer'   => 'required',
            'evaluations.salesman.questions.*.blocker'  => 'required',
            'customer.firstName'                        => 'required',
            'customer.lastName'                         => 'required',
            'customer.email'                            => 'required|email',
            'customer.cpf'                              => 'required|cpf',
            'customer.gender'                           => ['required', Rule::in(['M', 'F'])],
            'customer.birthday'                         => 'required|date_format:"' . Formats::DATE . '"',
            'customer.filiation'                        => 'required',
            'customer.mainPhone'                        => 'required',
            'customer.rg'                               => 'required',
            'customer.rgLocal'                          => 'required',
            'customer.rgDate'                           => 'required',
            'customer.zipCode'                          => 'required',
            'customer.neighborhood'                     => 'required',
            'customer.local'                            => 'required',
            'customer.city'                             => 'required',
            'customer.number'                           => 'required',
            'customer.state'                            => 'required',
            'customer.complement'                       => 'sometimes'
        ];
    }
}
