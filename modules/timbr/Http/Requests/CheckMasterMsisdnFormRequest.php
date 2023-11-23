<?php

declare(strict_types=1);

namespace TimBR\Http\Requests;

use TradeAppOne\Domain\Enumerators\Formats;
use TradeAppOne\Http\Requests\FormRequestAbstract;

class CheckMasterMsisdnFormRequest extends FormRequestAbstract
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
            'masterMsisdn'       => 'required|numeric',
            'customer'           => 'required',
            'customer.cpf'       => 'required|cpf',
        ];
    }
}
