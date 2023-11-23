<?php


namespace Generali\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class GeneraliFormRequest extends FormRequestAbstract
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $method = $this->route()->getActionMethod();

        return self::actions($method);
    }

    public static function actions(string $method): array
    {
        $action =  [
            'plans'       => self::onPlans(),
            'eligibility' => self::onEligibility(),
            'updateInsurance' => self::onUpdateInsurance()
        ];

        return array_key_exists($method, $action)
            ? $action[$method]
            : [
                'serviceTransaction' => 'sometimes',
                'reference'          => 'sometimes'
            ];
    }

    public static function onPlans(): array
    {
        return [
            'productPartnerId'=> 'required|integer',
        ];
    }

    private static function onEligibility(): array
    {
        return [
            'devicePrice' => 'required',
            'deviceDate'  => 'sometimes',
            'slug'        => 'required|string'
        ];
    }

    private static function onUpdateInsurance(): array
    {
        return [
            'insurers' => 'required|array'
        ];
    }
}
