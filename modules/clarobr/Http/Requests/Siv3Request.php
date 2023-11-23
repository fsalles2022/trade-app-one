<?php

declare(strict_types=1);

namespace ClaroBR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use TradeAppOne\Domain\Enumerators\Operations;

class Siv3Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return mixed[] */
    public function rules(): array
    {
        $rules = [
            'sendAuthorization' => $this->checkAuthenticityRules(),
            'checkAuthorization' => $this->checkAuthorizationCodeRules()
        ];

        return $rules[request()->route()->getActionMethod()] ?? [];
    }

    /** @return string[] */
    public function checkAuthenticityRules(): array
    {
        return [
            'type' => 'required|string',
            'customer' => 'required|array',
            'customer.phoneNumber' => 'required|string',
            'origin' => 'required|string',
        ];
    }

    /** @return string[] */
    public function checkAuthorizationCodeRules(): array
    {
        return [
            'code' => 'required|string',
            'phoneNumber' => 'required|string'
        ];
    }
}
