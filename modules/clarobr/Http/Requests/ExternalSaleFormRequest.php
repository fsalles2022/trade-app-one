<?php

declare(strict_types=1);

namespace ClaroBR\Http\Requests;

use Illuminate\Validation\Rule;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Http\Requests\FormRequestAbstract;

class ExternalSaleFormRequest extends FormRequestAbstract
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return string[] */
    public function rules(): array
    {
        return [
            'mode' => Rule::in([Modes::ACTIVATION, Modes::PORTABILITY]),
            'areaCode' => 'required|area_code_prefix',
            'msisdn' => 'required|digits:11',
            'iccid' => 'nullable|digits:20',
            'customerCpf' => 'required|cpf',
            'email' => 'email'
        ];
    }
}
