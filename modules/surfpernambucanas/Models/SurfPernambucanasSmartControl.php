<?php

declare(strict_types=1);

namespace SurfPernambucanas\Models;

use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Rules\GreaterThan;

class SurfPernambucanasSmartControl extends Service
{
    /** @param mixed[] $attributes */
    public function fill(array $attributes): self
    {
        $this->fillable = array_merge(
            parent::getFillable(),
            [
                'msisdn',
                'iccid',
                'areaCode',
                'portedNumber',
                'mode',
                'invoiceType',
                'fromOperator',
                'portinDate',
                'type',
                'recurrence',
                'donate_chip'
            ]
        );

        return parent::fill($attributes);
    }

    /** @return string[] */
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'donate_chip.discount' => [
                    'sometimes', 'required', 'numeric', new GreaterThan(0)
                ],
                'fromOperator' => 'required_if:mode,' . Modes::PORTABILITY
            ]
        );
    }

    /** @return string[] */
    public function messages(): array
    {
        return array_merge(
            parent::messages(),
            [
                'fromOperator.required_if' => trans('surfpernambucanas::messages.validation.fromOperatorMessage')
            ]
        );
    }

    public function save(array $options = [])
    {
        $this->recurrence = true;

        return parent::save($options);
    }
}
