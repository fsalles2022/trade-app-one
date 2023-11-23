<?php


namespace Outsourced\ViaVarejo\DataTransferObjects;

use Outsourced\ViaVarejo\Helpers\UserCacheHelper;
use TradeAppOne\Domain\Models\Collections\Service;

class ViaVarejoBase
{
    protected const CODIGO_EMPRESA = 21;
    protected $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    public function getAreaCode(): int
    {
        $areaCode = data_get($this->service, 'areaCode');
        if ($areaCode !== null) {
            return (int) $areaCode;
        }
        $msisdn = data_get($this->service, 'msisdn');
        if ($msisdn !== null) {
            return (int) substr($msisdn, 0, 2);
        }
        return 0;
    }

    public function getPhoneNumber($number): int
    {
        if (strlen($number) > 11) {
            return (int) substr($number, -11);
        }

        return (int) $number;
    }

    public function getFTM(): array
    {
        return [
            'idTradeUp' => $this->service->serviceTransaction,
            'dataVenda' => $this->service->sale->getAttribute('createdAt')->format('Y-m-d')
        ];
    }

    public function getCustomer(): array
    {
        $customer  = $this->service->customer;
        $mainPhone = data_get($customer, 'mainPhone');

        return [
            'cpf' => data_get($customer, 'cpf'),
            'nomeCliente' => data_get($customer, 'firstName') .' '. data_get($customer, 'lastName'),
            'ddd' => substr($mainPhone, 3, 2),
            'numTelefone' => substr($mainPhone, 5),
            'generoCliente' => data_get($customer, 'gender'),
            'dataNascimento' => data_get($customer, 'birthday'),
            'nomeMae' => data_get($customer, 'filiation'),
            'dddTelefoneContato' => substr($mainPhone, 3, 2),
            'numTelefoneContato' => substr(data_get($customer, 'secondaryPhone'), 5),
            'rg' => data_get($customer, 'rg'),
            'rgDigitoVerificador' => '',
            'rgDataEmissao' => '',
            'rgUfEmissao' => data_get($customer, 'rgState'),
            'rgOrgaoEmissor' => data_get($customer, 'rgLocal'),
            'email' => data_get($customer, 'email'),
        ];
    }

    public function getAddress():array
    {
        $customer = $this->service->customer;

        return [
            'cep' => data_get($customer, 'zipCode'),
            'logradouro' => data_get($customer, 'local'),
            'numero' => data_get($customer, 'number'),
            'bairro' => data_get($customer, 'neighborhood'),
            'idCidade' => '',
            'cidade' => 'city',
            'estado' => data_get($customer, 'state'),
        ];
    }

    public function getSalesMan():array
    {
        $pointOfSale     = $this->service->sale->pointOfSale;
        $pointOfSaleSlug = data_get($pointOfSale, 'slug');

        $pointOfSaleCodeAlternate = UserCacheHelper::make()->getViaVarejoUserPointOfSaleAlternateByUserId(
            (int) data_get($this->service->sale, 'user.id')
        );

        return  [
            'codigoEmpresa' => self::CODIGO_EMPRESA,
            'codigoFilial' => (int) ($pointOfSaleCodeAlternate ?: $pointOfSaleSlug),
            'dddFilial' => (int) data_get($pointOfSale, 'areaCode'),
            'estadoFilial' => data_get($pointOfSale, 'state'),
            'numMatricula' => (int) data_get($this->service->sale, 'userAlternate.document'),
        ];
    }

    public function getCampaign():array
    {
        return [
            'idCampanhaVV' => data_get($this->service, 'discount.coupon.campaign'),
            'cupomCampanhaVV' => data_get($this->service, 'discount.coupon.coupon'),
        ];
    }

    public function getAutomaticDebit(): array
    {
        return [
            'idBanco' => data_get($this->service, 'bankId', 0),
            'agencia' => data_get($this->service, 'agency', 0),
            'digitoAgencia' => 0,
            'conta' => (int) data_get($this->service, 'checkingAccount', 0),
            'digitoConta' => 0
        ];
    }
}
