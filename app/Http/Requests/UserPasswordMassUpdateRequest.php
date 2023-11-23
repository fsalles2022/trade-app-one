<?php

declare(strict_types=1);

namespace TradeAppOne\Http\Requests;

use TradeAppOne\Domain\Enumerators\Permissions\ImportablePermission;

class UserPasswordMassUpdateRequest extends FormRequestAbstract
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission(ImportablePermission::NAME . '.' . ImportablePermission::USER_PASSWORD_MASS_UPDATE);
    }

    public function rules(): Array
    {
        return [];
    }
}
