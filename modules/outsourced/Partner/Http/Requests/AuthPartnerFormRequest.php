<?php


namespace Outsourced\Partner\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class AuthPartnerFormRequest extends FormRequestAbstract
{

    public const ACCESS_KEY_REQUIRED = 'accessKey.required';
    public const TOKEN_REQUIRED      = 'token.required';
    public const ROUTE_REQUIRED      = 'route.required';

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'accessKey' => 'required|string|min:1|max:255',
            'token' => 'required|string|min:3',
            'route' => 'sometimes|required|string|min:1'
        ];
    }

    public function messages()
    {
        return [
            self::ACCESS_KEY_REQUIRED => trans('partner::validate_messages.' . self::ACCESS_KEY_REQUIRED),
            self::TOKEN_REQUIRED  => trans('partner::validate_messages.' . self::TOKEN_REQUIRED),
            self::ROUTE_REQUIRED  => trans('partner::validate_messages.' . self::ROUTE_REQUIRED),
        ];
    }
}
