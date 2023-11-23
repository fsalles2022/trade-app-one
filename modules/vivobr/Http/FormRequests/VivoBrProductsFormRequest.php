<?php

namespace VivoBR\Http\FormRequests;

use Illuminate\Validation\Rule;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Http\Requests\FormRequestAbstract;

class VivoBrProductsFormRequest extends FormRequestAbstract
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'areaCode' => 'sometimes|area_code_prefix|numeric',
            'mode' => 'sometimes',
            'operation' => ['sometimes', Rule::in(
                [
                    Operations::VIVO_PRE,
                    Operations::VIVO_CONTROLE,
                    Operations::VIVO_CONTROLE_CARTAO,
                    Operations::VIVO_POS_PAGO,
                    Operations::VIVO_INTERNET_MOVEL_POS
                ]
            )
            ]
        ];
    }
}
