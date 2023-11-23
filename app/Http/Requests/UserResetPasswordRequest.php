<?php


namespace TradeAppOne\Http\Requests;

use Illuminate\Http\Request;

class UserResetPasswordRequest extends FormRequestAbstract
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [];
        switch (request()->getMethod()) {
            case Request::METHOD_POST:
                $rules = [
                    'password' => 'required|string|min:6'
                ];
                break;
            case Request::METHOD_PUT:
                $rules = [
                    'password' => 'required|string|min:6',
                    'verificationToken' => 'required|string',
                ];
                break;
        }
        return $rules;
    }
}
