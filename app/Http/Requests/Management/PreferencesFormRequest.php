<?php

namespace TradeAppOne\Http\Requests\Management;

use Illuminate\Validation\Rule;
use TradeAppOne\Domain\Enumerators\NetworkPreferences;
use TradeAppOne\Http\Requests\FormRequestAbstract;

class PreferencesFormRequest extends FormRequestAbstract
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'preference' => ['required', Rule::in(NetworkPreferences::PREFERENCES)],
            'value'      => 'boolean'
        ];
    }
}
