<?php

declare(strict_types=1);

namespace TimBR\Http\Requests;

use TradeAppOne\Domain\Enumerators\Formats;
use TradeAppOne\Http\Requests\FormRequestAbstract;

class CreditAnalysisFormRequest extends FormRequestAbstract
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return mixed[] */
    public function rules(): array
    {
        return [
            'pointOfSaleId'      => 'required|numeric',
            'operation'          => 'required|string',
            'areaCode'           => 'required|numeric',
            'customer'           => 'required',
            'customer.firstName' => 'required|' . Formats::NAMES,
            'customer.lastName'  => 'required|' . Formats::NAMES,
            'customer.cpf'       => 'required|cpf',
            'customer.birthday'  => 'required| date_format:"' . Formats::DATE . '"',
            'customer.gender'    => 'required|in:F,M',
            'customer.filiation' => 'required|' . Formats::NAMES,
            'customer.mainPhone' => 'required|numeric',
            'customer.email'     => 'required|email',
            'customer.zipCode'   => 'required|numeric',
            'customer.number'    => 'required|numeric',
            'customer.rg'        => 'required',
            'customer.rgDate'    => 'required| date_format:"' . Formats::DATE . '"',
            'customer.rgLocal'   => 'required|string',
            'customer.rgState'   => 'required|string',
            'product.id'         => 'required|string',
            'product.name'       => 'required|string',
            'services.*.id'      => 'sometimes|string',
            'packages.*.id'      => 'sometimes|string',
            'loyalties.*.id'     => 'sometimes|string',
        ];
    }
}
