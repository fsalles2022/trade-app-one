<?php

namespace OiBR\Assistance;

use OiBR\Adapters\OiBRControleBoletoEligibilityResponseAdapter;
use OiBR\Adapters\OiBRControleCartaoEligibilityResponseAdapter;
use OiBR\Connection\ElDoradoGateway\ElDoradoConnection;
use OiBR\Connection\OiBRConnection;
use OiBR\Enumerators\OiBRInvoiceTypes;
use OiBR\OiBRIdentifierNotFound;
use OiBR\Services\OiBRMapPlansService;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Services\BaseService;

class OiBRService extends BaseService
{
    protected $oiBRConnection;
    protected $elDoradoConnection;

    public function __construct(OiBRConnection $oiBRConnection, ElDoradoConnection $elDoradoConnection)
    {
        $this->oiBRConnection     = $oiBRConnection;
        $this->elDoradoConnection = $elDoradoConnection;
    }

    public function getCreditCards(string $msisdn)
    {
        return $this->elDoradoConnection->getCreditCards($msisdn);
    }

    public function getPlans(string $pointOfSale, string $areaCode, string $invoiceType = '')
    {
        $pointOfSale = $this->pointOfSaleService->find($pointOfSale);
        try {
            $oiIdentifier = $pointOfSale->providerIdentifiers[Operations::OI];
        } catch (\Exception $exception) {
            throw new OiBRIdentifierNotFound();
        }
        throw_if(is_null($oiIdentifier), new OiBRIdentifierNotFound());
        if ($invoiceType == OiBRInvoiceTypes::BOLETO) {
            $plans = $this->oiBRConnection->getPlans($oiIdentifier, $areaCode, OiBRInvoiceTypes::BOLETO)->toArray();
            return OiBRMapPlansService::map($plans, ['operation' => Operations::OI_CONTROLE_BOLETO]);
        } elseif ($invoiceType == OiBRInvoiceTypes::CARTAO_CREDITO) {
            $plans = $this->oiBRConnection->getPlans(
                $oiIdentifier,
                $areaCode,
                OiBRInvoiceTypes::CARTAO_CREDITO
            )->toArray();
            return OiBRMapPlansService::map($plans, ['operation' => Operations::OI_CONTROLE_CARTAO]);
        }

        $boletoPlans = $this->oiBRConnection->getPlans($oiIdentifier, $areaCode, 'boleto_bancario')->toArray();
        $boletoPlans = OiBRMapPlansService::map($boletoPlans, ['operation' => Operations::OI_CONTROLE_BOLETO]);

        $cartaoPlans = $this->oiBRConnection->getPlans($oiIdentifier, $areaCode, 'cartao_credito')->toArray();
        $cartaoPlans = OiBRMapPlansService::map($cartaoPlans, ['operation' => Operations::OI_CONTROLE_CARTAO]);
        return $cartaoPlans->merge($boletoPlans);
    }

    public function registerCreditCard(string $pan, string $year, string $month)
    {
        return $this->oiBRConnection->registerCreditCard($pan, $year, $month)->toArray();
    }

    public function eligibility(string $msisdn, ?string $type)
    {
        if ($type == Operations::OI_CONTROLE_BOLETO) {
            $response = $this->oiBRConnection->controleBoletoEligibility($msisdn);
            $adapted  = new OiBRControleBoletoEligibilityResponseAdapter($response);
            return $adapted->adapt();
        }
        if ($type == Operations::OI_CONTROLE_CARTAO) {
            $response = $this->oiBRConnection->controleCartaoEligibility($msisdn);
            $adapted  = new OiBRControleCartaoEligibilityResponseAdapter($response);
            return $adapted->adapt();
        }
    }

    /**
    * @return string[]
    */
    public function getResidentialLinks(): array
    {
        return [
            'oiSaleFlow' => config('integrations.oiBR.oiSaleFlow'),
            'documentCertification' => config('integrations.oiBR.documentCertification')
        ];
    }
}
