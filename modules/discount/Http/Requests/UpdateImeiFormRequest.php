<?php

declare(strict_types=1);

namespace Discount\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateImeiFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return string[] */
    public function rules(): array
    {
        $rules = [
            'getImei' => $this->rulesGetImei(),
            'authorizeUpdateImei' => $this->rulesAuthorize(),
            'updateImei' => $this->rulesUpdateImei()
        ];

        return $rules[$this->route()->getActionMethod()] ?? [];
    }

    /** @return string[] */
    private function rulesGetImei(): array
    {
        return [
            'serviceTransaction' => 'sometimes|string',
            'cpf' => 'sometimes|cpf'
        ];
    }

    /** @return string[] */
    public function rulesAuthorize(): array
    {
        return [
            'login' => 'required|string',
            'password' => 'required|string',
            'serviceTransaction' => 'required|string'
        ];
    }

    /** @return string[] */
    public function rulesUpdateImei(): array
    {
        return [
            'token' => 'required|string',
            'authorizerCpf' => 'required|string|cpf',
            'serviceTransaction' => 'required|string',
            'newImei' => 'required|string',
            'oldImei' => 'required|string',
            'customerCpf' => 'required|string|cpf'
        ];
    }
}
