<?php

namespace Banner\Http\Request;

use Banner\Enumerators\BannerStatus;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class BannerEditFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'imageDesktop' => 'mimes:jpeg,bmp,png',
            'imageTablet'  => 'mimes:jpeg,bmp,png',
            'imageMobile'  => 'mimes:jpeg,bmp,png',
            'startAt'      => 'date',
            'endAt'        => 'date',
            'order'        => 'numeric',
            'status'       => [Rule::in(BannerStatus::STATUS)],
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
