<?php

namespace FastShop\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class SimulationFormRequest extends FormRequestAbstract
{
    private const PRODUCT_SIMULATION = 'productSimulation';

    private const ROUTER_PARAMETERS_TO_VALIDATE = [
        'pos' => 'pos',
        'device' => 'device'
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function all($keys = null): array
    {
        $input = parent::all();
        foreach (self::ROUTER_PARAMETERS_TO_VALIDATE as $validationDataKey => $routeParameter) {
            $input[$validationDataKey] = $this->route($routeParameter);
        }
        return $input;
    }

    public function rules(): ?array
    {
        $action = $this->route()->getActionMethod();

        switch ($action) {
            case self::PRODUCT_SIMULATION:
                return $this->onSimulate();
            default:
                return [];
        }
    }

    private function onSimulate(): array
    {
        return [
            'pos'       => 'required|string',
            'device'    => 'required|string'
        ];
    }
}
