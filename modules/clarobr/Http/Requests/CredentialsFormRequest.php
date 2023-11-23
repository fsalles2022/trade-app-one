<?php

namespace ClaroBR\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class CredentialsFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'cpf' => 'required|cpf',
            'password' => 'required',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $content  = ['errors' => $validator->errors()];
        $response = response()->json($content, Response::HTTP_UNPROCESSABLE_ENTITY);
        throw new HttpResponseException($response);
    }
}
