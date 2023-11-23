<?php

declare(strict_types=1);

namespace TimBR\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class BrScanAuthenticateIdFormRequest extends FormRequestAbstract
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'authenticateId' => 'required|numeric',
        ];
    }
}
