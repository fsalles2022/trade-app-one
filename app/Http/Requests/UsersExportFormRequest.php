<?php

namespace TradeAppOne\Http\Requests;

use Illuminate\Validation\Rule;
use TradeAppOne\Domain\Components\Helpers\ConstantHelper;
use TradeAppOne\Domain\Enumerators\NetworkEnum;

class UsersExportFormRequest extends FormRequestAbstract
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return ['networks' => 'sometimes|array', Rule::in(ConstantHelper::getAllConstants(NetworkEnum::class))];
    }
}
