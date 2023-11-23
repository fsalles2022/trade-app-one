<?php

namespace OiBR\Assistance;

use Illuminate\Support\Facades\Auth;
use OiBR\Connection\OiBRConnection;
use OiBR\OiBRIdentifierNotFound;
use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Services\MountNewAttributesService;
use TradeAppOne\Exceptions\BusinessExceptions\ProductNotFoundException;

class MountNewAttributeFromOiBR implements MountNewAttributesService
{
    protected $oiBRConnection;

    public function __construct(OiBRConnection $oiBRConnection)
    {
        $this->oiBRConnection = $oiBRConnection;
    }

    public function getAttributes(array $service): array
    {
        try {
            $pointOfSale = Auth::user()->pointsOfSale()->first();
            try {
                $oiIdentifier = $pointOfSale->providerIdentifiers[Operations::OI];
            } catch (\Exception $exception) {
                throw new OiBRIdentifierNotFound();
            }
            throw_if(is_null($oiIdentifier), new OiBRIdentifierNotFound());
            $invoiceType = $service['operation'] == Operations::OI_CONTROLE_BOLETO ? 'boleto_bancario' : 'cartao_credito';
            $msisdn      = data_get($service, 'msisdn');
            if ($msisdn) {
                $areaCode = MsisdnHelper::getAreaCode($msisdn);
            } else {
                $areaCode = data_get($service, 'areaCode');
            }

            $plans = $this->oiBRConnection
                ->getPlans($oiIdentifier, $areaCode, $invoiceType)
                ->toArray();

            $collectionOfPlans = collect($plans);
            $plan              = $collectionOfPlans->where('nome', $service['product'])->first();
            $label             = $plan['nomeComercial'];
            $valueAdhesion     = $plan['valorAdesao'] / 100;
            $price             = $plan['valorRecorrencia'] / 100;
            return compact('label', 'price', 'valueAdhesion', 'areaCode', 'invoiceType');
        } catch (\Exception $exception) {
            throw  new ProductNotFoundException();
        }
    }
}
