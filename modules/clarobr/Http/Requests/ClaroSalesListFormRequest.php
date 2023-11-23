<?php

declare(strict_types=1);

namespace ClaroBR\Http\Requests;

use ClaroBR\Enumerators\SivOperations;
use TradeAppOne\Http\Requests\FormRequestAbstract;
use Illuminate\Validation\Rule;
use TradeAppOne\Domain\Components\Helpers\ConstantHelper;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;

class ClaroSalesListFormRequest extends FormRequestAbstract
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return mixed[]
     */
    public function rules(): array
    {
        return [
            'status'  => ['sometimes', Rule::in(ConstantHelper::getAllConstants(ServiceStatus::class))],
            'operation'  => ['sometimes', Rule::in([
                Operations::CLARO_CONTROLE_BOLETO,
                Operations::CLARO_CONTROLE_FACIL,
                Operations::CLARO_PRE,
                Operations::CLARO_POS,
                Operations::CLARO_BANDA_LARGA,
                Operations::CLARO_FIXO,
                Operations::CLARO_TV_PRE,
                Operations::CLARO_TELEVISAO,
                Operations::CLARO_RESIDENCIAL,
                SivOperations::BANDA_LARGA,
                SivOperations::PONTO_ADICIONAL,
                SivOperations::BANDA_LARGA_NET,
                SivOperations::FIXO_NET,
                SivOperations::TELEVISAO_NET
            ])],
            'startDate'  => 'sometimes|date',
            'endDate'  => 'sometimes|date',
        ];
    }
}
