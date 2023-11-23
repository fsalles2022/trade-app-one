<?php

declare(strict_types=1);

namespace ClaroBR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckExternalSaleFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return string[] */
    public function rules(): array
    {
        return [
            'areaCode' => 'required|area_code_prefix',
            'customerCpf' => 'required|cpf',
            'iccid' => 'nullable|digits:20',
            'msisdn' => 'required|string|max:11',
            'mode' => 'required|string',
            'operation' => 'required|string',
        ];
    }
}
