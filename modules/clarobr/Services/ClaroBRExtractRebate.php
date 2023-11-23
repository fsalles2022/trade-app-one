<?php

namespace ClaroBR\Services;

use ClaroBR\Adapters\ResponseModels\ClaroBRRebateResponseModel;
use ClaroBR\Connection\SivConnectionInterface;
use ClaroBR\Connection\SivRoutes;
use ClaroBR\Exceptions\AttributeNotFound;
use ClaroBR\Exceptions\RebateNotFound;
use Illuminate\Support\Facades\Auth;

class ClaroBRExtractRebate
{
    protected $connection;

    public function __construct(SivConnectionInterface $sivConnection)
    {
        $this->connection = $sivConnection;
    }

    public function extract($plan, $device, $areaCode, $from): array
    {
        $pointOfSale = $this->connection->getIdentifiers(SivRoutes::ENDPOINT_PDV_USER, Auth::user()->cpf)->toArray();
        $network     = $this->connection
            ->pointOfSaleBy(['id' => data_get($pointOfSale, 'data.0.id')])->toArray();

        $network = data_get($network, 'data.network.nome');

        throw_if($network === null, new AttributeNotFound('Rede Siv'));

        $model   = data_get($device, 'model');
        $service = [
            'network'  => $network,
            'plan'     => mb_strtolower($plan),
            'model'    => $model,
            'areaCode' => $areaCode,
            'from'     => $from
        ];
        $rebate  = $this->connection->rebate($service)->toArray();
        return (new ClaroBRMapSale())->extractRebate($rebate, $model)->toArray();
    }

    public function extractRebate(array $rebate, string $model = ''): ClaroBRRebateResponseModel
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
