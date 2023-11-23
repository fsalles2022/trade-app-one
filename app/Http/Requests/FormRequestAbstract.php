<?php

namespace TradeAppOne\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

abstract class FormRequestAbstract extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        $content['errors'] = [];
        foreach ($validator->errors()->all() as $message) {
            array_push($content['errors'], ['message' => $message]);
        }

        $response = response()->json($content, Response::HTTP_UNPROCESSABLE_ENTITY);
        throw new HttpResponseException($response);
    }
}
