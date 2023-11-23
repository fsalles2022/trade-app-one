<?php

declare(strict_types=1);

namespace TimBR\Models;

use TradeAppOne\Domain\Models\Collections\Service;

class TimBRControleFlex extends Service
{
    /** @param mixed[] $attributes */
    public function fill(array $attributes): self
    {
        $this->fillable = array_merge(
            parent::getFillable(),
            [
                'msisdn',
                'iccid',
                'invoiceType',
                'billType',
                'mode',
                'areaCode',
                'portedNumber',
                'dueDate',
                'eligibilityToken',
                'loyalty',
                'productName',
                'directDebit',
                'package',
                'automaticPackages',
                'promoter',
                'selectedServices',
                'timProtocolSearchTries'
            ]
        );

        return parent::fill($attributes);
    }
}
