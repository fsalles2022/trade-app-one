<?php

namespace TradeAppOne\Http\Requests;

use Illuminate\Http\Request;

class RoleFormRequest extends FormRequestAbstract
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        switch (request()->getMethod()) {
            case Request::METHOD_POST:
                return $this->onPost();
                break;

            case Request::METHOD_PUT:
                return $this->onPut();
                break;

            default:
                return $this->onGet();
        }
    }

    private function onPost()
    {
        return [
            'name'            => 'required|string|name',
            'parent'          => 'numeric|exists:roles,id',
            'networkSlug'     => 'required|string|exists:networks,slug',
            'permissionsSlug' => 'required|array|exists:permissions,slug'
        ];
    }

    private function onPut()
    {
        return [
            'name'            => 'required|string|name',
            'networkSlug'     => 'required|string|exists:networks,slug',
            'permissionsSlug' => 'required|array|exists:permissions,slug'
        ];
    }

    private function onGet()
    {
        return [
            'name'    => 'sometimes|string',
            'network' => 'sometimes|string',
        ];
    }
}
