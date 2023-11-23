<?php

namespace ClaroBR\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class SivFormRequest extends FormRequestAbstract
{
    public const PROMOTER_AUTH = 'promoterAuth';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $action = $this->route()->getActionMethod();

        if ($action === self::PROMOTER_AUTH) {
            return $this->promoterAuth();
        }

        return [
            'cnpj' => 'sometimes'
        ];
    }

    private function promoterAuth(): array
    {
        return [
            'username'   => 'required',
            'password'   => 'required',
            'msisdn'     => 'required',
            'cpf'        => 'sometimes|cpf',
            'token'      => 'sometimes',
            'codigo_pdv' => 'sometimes'
        ];
    }
}
