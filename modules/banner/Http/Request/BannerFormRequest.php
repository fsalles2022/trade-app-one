<?php

namespace Banner\Http\Request;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class BannerFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'imageDesktop' => 'required|mimes:jpeg,bmp,png',
            'imageTablet'  => 'required|mimes:jpeg,bmp,png',
            'imageMobile'  => 'required|mimes:jpeg,bmp,png',
            'startAt'      => 'date|before_or_equal:endAt',
            'endAt'        => 'date|after_or_equal:startAt',
            'href'         => 'sometimes'
        ];
    }

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
