<?php


namespace Outsourced\GPA\DataTransferObjects;

use TradeAppOne\Domain\Models\Collections\Service;

abstract class GpaDTO
{
    /**
     * @var Service
     */
    protected static $service;

    public function __construct(Service $service)
    {
        self::$service = $service;
    }

    protected static function getInfo(): array
    {
        $createAt = self::$service->sale->getAttribute('createdAt');
        return [
            'idTradeUp' => self::$service->serviceTransaction ?? '',
            'dataVenda' => $createAt
                ? $createAt->format('Y-m-d') . 'T' . $createAt->format('H:i:s')
                : ''
        ];
    }

    protected static function getDevice(): array
    {
        return array(
            'descricao' => data_get(self::$service, 'device.label', ''),
            'modelo' => data_get(self::$service, 'device.model', ''),
            'precoComDesconto' => data_get(self::$service, 'device.priceWith', 0),
            'precoSemDesconto' => data_get(self::$service, 'device.priceWithout', 0),
            'sku' => data_get(self::$service, 'device.sku', '')
        );
    }

    protected static function getCustomer(): array
    {
        $customer  = self::$service->customer;
        $mainPhone = data_get($customer, 'mainPhone', '');

        return [
            'cpf' => data_get($customer, 'cpf', ''),
            'nomeCliente' => data_get($customer, 'firstName', '') .' '. data_get($customer, 'lastName', ''),
            'ddd' => (string) substr($mainPhone, 3, 2),
            'numTelefone' => (string) substr($mainPhone, 5),
            'dataNascimento' => data_get($customer, 'birthday', ''),
            'email' => data_get($customer, 'email', ''),
        ];
    }

    protected static function getAddress():array
    {
        $customer = self::$service->customer;

        return [
            'cep' => data_get($customer, 'zipCode', ''),
            'logradouro' => data_get($customer, 'local', ''),
            'numero' => data_get($customer, 'number', ''),
            'bairro' => data_get($customer, 'neighborhood', ''),
            'cidade' => data_get($customer, 'city', ''),
            'estado' => data_get($customer, 'state', ''),
        ];
    }

    protected static function getSalesMan():array
    {
        $sale        = self::$service->sale;
        $pointOfSale = $sale->pointOfSale;

        return  [
            'codigoFilial' => (int) data_get($pointOfSale, 'slug', 0),
            'dddFilial' => (int) data_get($pointOfSale, 'areaCode', 0),
            'estadoFilial' => data_get($pointOfSale, 'state', ''),
            'cpf' => (int) data_get($sale->user, 'cpf', 0),
        ];
    }

    protected static function getCampaign():array
    {
        return [
            'campanhaTriangulacao' => data_get(self::$service, 'discount.title', ''),
            'descontoTriangulacao' => data_get(self::$service, 'discount.discount', 0),
        ];
    }

    protected static function getAutomaticDebit(): array
    {
        return [
            'idBanco' => data_get(self::$service, 'bankId', 0),
            'agencia' => data_get(self::$service, 'agency', 0),
            'digitoAgencia' => 0,
            'conta' => data_get(self::$service, 'checkingAccount', 0),
            'digitoConta' => 0
        ];
    }

    protected static function getAreaCode(): int
    {
        $areaCode = data_get(self::$service, 'areaCode', static function () {
            return data_get(self::$service, 'msisdn', 0);
        });

        return (int) substr($areaCode, 0, 2);
    }

    protected static function getFidelity(?array $promotion = null): string
    {
        $label = data_get($promotion, 'label');
        return preg_match('/fidel*/i', $label)
            ? 'S'
            : 'N';
    }

    abstract protected static function plan(): array;
}
