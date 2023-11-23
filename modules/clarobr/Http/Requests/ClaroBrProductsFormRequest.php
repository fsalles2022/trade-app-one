<?php

namespace ClaroBR\Http\Requests;

use Illuminate\Validation\Rule;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Http\Requests\FormRequestAbstract;

class ClaroBrProductsFormRequest extends FormRequestAbstract
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'areaCode' => 'sometimes|area_code_prefix|numeric',
            'mode' => 'sometimes|string',
            'product' => 'sometimes|string',
            'operation' => [
                'sometimes',
                Rule::in(
                    [
                        Operations::CLARO_CONTROLE_BOLETO,
                        Operations::CLARO_CONTROLE_FACIL,
                        Operations::CLARO_PRE,
                        Operations::CLARO_POS,
                        Operations::CLARO_BANDA_LARGA,
                    ]
                )
            ]
        ];
    }
}
