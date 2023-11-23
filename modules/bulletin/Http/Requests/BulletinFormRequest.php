<?php

declare(strict_types=1);

namespace Bulletin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulletinFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return string[] */
    public function rules(): array
    {
        $method = $this->route()->getActionMethod();
        return self::actions($method);
    }

    /**
     * @param string $method
     * @return string[]
     */
    public static function actions(string $method): array
    {
        $action =  [
            'activate' => self::activate(),
            'store'    => self::store(),
            'update'   => self::update(),
        ];

        return array_key_exists($method, $action)
            ? $action[$method]
            : [
                'serviceTransaction' => 'sometimes',
                'reference'          => 'sometimes'
            ];
    }

    /** @return string[] */
    private static function activate(): array
    {
        return [
            'status' => 'required|boolean',
        ];
    }

    /** @return string[] */
    private static function store(): array
    {
        return [
            'data' => 'required',
            'data.*.title' => 'required|string|max:70',
            'data.*.description' => 'required|string|max:240',
            'data.*.period.startDate' => 'required|date|before_or_equal:period.endDate',
            'data.*.period.endDate' => 'required|date|after_or_equal:period.startDate',
            'imageDesktop' => 'required|mimes:jpeg,bmp,png',
            'data.*.status' => 'required',
        ];
    }

    /** @return string[] */
    private static function update(): array
    {
        return [
            'title' => 'sometimes|string|max:70',
            'description' => 'sometimes|string|max:240',
            'period.startDate' => 'sometimes|date|before_or_equal:period.endDate',
            'period.endDate' => 'sometimes|date|after_or_equal:period.startDate',
            'imageDesktop' => 'sometimes|mimes:jpeg,bmp,png',
        ];
    }
}
