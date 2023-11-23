<?php


namespace TradeAppOne\Http\Requests;

use Illuminate\Validation\Rule;
use TradeAppOne\Domain\Components\Helpers\ConstantHelper;
use TradeAppOne\Domain\Enumerators\ServiceStatus;

class EditStatusFormRequest extends FormRequestAbstract
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'serviceTransaction' => 'required',
            'status'             => ['sometimes', Rule::in(ConstantHelper::getAllConstants(ServiceStatus::class))],
            'imei'               => 'sometimes|required',
            'iccid'              => 'sometimes|required',
        ];
    }
}
