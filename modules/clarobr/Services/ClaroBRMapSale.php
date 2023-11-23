<?php

namespace ClaroBR\Services;

use ClaroBR\Adapters\ResponseModels\ClaroBRPlanResponseModel;
use ClaroBR\Adapters\ResponseModels\ClaroBRRebateResponseModel;
use ClaroBR\Exceptions\RebateNotFound;
use TradeAppOne\Exceptions\BusinessExceptions\ProductNotFoundException;

class ClaroBRMapSale
{
    protected $model;

    public function extractProductAttributes(
        array $plans,
        $product,
        $areaCode,
        $promotion = null
    ): ClaroBRPlanResponseModel {
        $this->model       = new ClaroBRPlanResponseModel();
        $collectionOfPlans = collect(data_get($plans, 'data.data', []));
        $plan              = $collectionOfPlans->first();

        $plansAreaCode = data_get($plan, 'plans_area_code');

        $planByAreaCode = collect($plansAreaCode)
            ->where('ddd', $areaCode)
            ->where('plano_id', $product)
            ->first();

        throw_if(is_null($planByAreaCode), new ProductNotFoundException());
        $this->model->slug    = $plan['nome'];
        $this->model->product = $planByAreaCode['plano_id'];
        $this->model->price   = (float) $planByAreaCode['valor'];
        $this->model->label   = $plan['label'];

        if ($promotion) {
            $this->extractPromotion($planByAreaCode, $promotion);
        }
        return $this->model;
    }

    private function extractPromotion($planByAreaCode, $product): void
    {
        $promotion                         = collect($planByAreaCode['promotions'])
            ->where('id', $product)
            ->first();
        $this->model->promotion['label']   = data_get($promotion, 'nome');
        $this->model->promotion['price']   = (float) data_get($promotion, 'valor');
        $this->model->promotion['product'] = $product;
        $this->model->price                = $this->model->price + $this->model->promotion['price'];
    }

    public function extractRebate(array $rebate, ?string $model = ''): ClaroBRRebateResponseModel
    {
        $rebateModel  = new ClaroBRRebateResponseModel();
        $priceWithout = data_get($rebate, 'data.rebate.valor_pre');
        $priceWith    = data_get($rebate, 'data.rebate.valor_plano');

        if (filled($priceWithout) && filled($priceWith)) {
            $rebateModel->model        = $model;
            $rebateModel->label        = mb_strtoupper(str_replace('_', ' ', $model));
            $rebateModel->priceWithout = (float) $priceWithout;
            $rebateModel->priceWith    = (float) $priceWith;
            $rebateModel->penalty      = (float) data_get($rebate, 'data.rebate.multa');
            return $rebateModel;
        }

        throw new RebateNotFound();
    }
}
