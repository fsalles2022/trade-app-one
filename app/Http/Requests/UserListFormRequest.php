<?php

declare(strict_types=1);

namespace TradeAppOne\Http\Requests;

use Illuminate\Validation\Rule;
use TradeAppOne\Domain\Components\Helpers\ConstantHelper;
use TradeAppOne\Domain\Enumerators\UserStatus;

class UserListFormRequest extends FormRequestAbstract
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return mixed[] */
    public function rules(): array
    {
        return [
            'firstName'    => 'sometimes',
            'cpf'          => 'sometimes|max:11',
            'pointsOfSale' => 'sometimes|array|exists:pointsOfSale,cnpj',
            'roles'        => 'sometimes|array|exists:roles,slug',
            'networks'     => 'sometimes|array|exists:networks,slug',
            'registration' => 'sometimes|string',
            'status'       => [
                'sometimes',
                'array',
                Rule::in(
                    array_values(ConstantHelper::getAllConstants(UserStatus::class))
                )
            ]
        ];
    }
}
