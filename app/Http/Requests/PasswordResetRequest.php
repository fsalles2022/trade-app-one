<?php

namespace TradeAppOne\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PasswordResetRequest extends FormRequestAbstract
{

    public function authorize()
    {
        if (request()->getMethod() == Request::METHOD_POST) {
            return true;
        }
        $user        = Auth::user();
        $permissions = $user->role()->first()->permissions['WEB'];
        if (array_key_exists('RECOVERY', $permissions) &&
            in_array("APPROVE", $permissions['RECOVERY'])) {
            return true;
        }
        return false;
    }

    public function rules()
    {
        if (request()->getMethod() == Request::METHOD_POST) {
            return $this->onPost();
        }
        return $this->onPut();
    }

    public function onPost()
    {
        return ['cpf'      => 'required|string', 'password' => 'sometimes'];
    }

    public function onPut()
    {
        return ['id' => 'required', 'response' => 'required'];
    }
}
