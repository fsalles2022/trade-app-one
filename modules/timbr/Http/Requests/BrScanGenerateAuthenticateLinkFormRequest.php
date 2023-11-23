<?php

declare(strict_types=1);

namespace TimBR\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class BrScanGenerateAuthenticateLinkFormRequest extends FormRequestAbstract
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer.cpf' => 'required|numeric',
            'customer.email' => 'required|email',
            'customer.phone' => 'required|numeric',
            'pointOfSaleId' => 'required|numeric',
        ];
    }
}
