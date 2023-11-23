<?php

declare(strict_types=1);

namespace ClaroBR\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class SivCheckAutomaticRegistrationStatusFormRequest extends FormRequestAbstract
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return mixed[] */
    public function rules(): array
    {
        return [
            'protocol' => 'required|string',
        ];
    }
}
