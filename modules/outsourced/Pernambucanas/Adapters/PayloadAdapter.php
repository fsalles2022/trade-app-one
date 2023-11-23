<?php

declare(strict_types=1);

namespace Outsourced\Pernambucanas\Adapters;

use Carbon\Carbon;
use Outsourced\Pernambucanas\Enumerators\PernambucanasPaymentType;
use TradeAppOne\Domain\Models\Collections\Service;

class PayloadAdapter implements PayloadInterface
{
    /** @var Service */
    private $service;

    /** @var mixed[] */
    private $payload;

    public function __construct(Service $service)
    {
        $this->service = $service;
        $this->payload = [];
    }

    public function toArray(): array
    {
        return $this->payload;
    }

    public function adapt(): self
    {
        $this->payload['info']                           = $this->infoProperties();
        $this->payload['plano']                          = $this->planProperties();
        $this->payload['aparelho']                       = $this->deviceProperties();
        $this->payload['cliente']                        = $this->customerProperties();
        $this->payload['endereco']                       = $this->addressProperties();
        $this->payload['vendedor']                       = $this->salesmanProperties();
        $this->payload['campanha']                       = $this->campaignProperties();
        $this->payload['dadosBancariosDebitoAutomatico'] = $this->automaticDebitProperties();

        return $this;
    }

    public function getOriginalService(): Service
    {
        return $this->service;
    }

    /** @return mixed[] */
    private function infoProperties(): array
    {
        return [
            'idTradeUp' => data_get($this->service, 'serviceTransaction', '000000000-0'),
            'dataVenda' => Carbon::parse(data_get($this->service->sale, 'createdAt', ''))
                ->format('Y-m-d H:i:s')
        ];
    }

    /** @return mixed[] */
    private function planProperties(): array
    {
        $operator    = data_get($this->service, 'operator', '');
        $invoiceType = data_get($this->service, 'invoiceType', 'default');

        return [
            'id' => (int) filter_var(
                data_get($this->service, 'product', 0),
                FILTER_SANITIZE_NUMBER_INT
            ),
            'valorTotalFinal' => (float) data_get($this->service, 'price', 0.00),
            'operadora' => $operator,
            'operacao' => data_get($this->service, 'operation', ''),
            'tipoPagamento' => PernambucanasPaymentType::get($operator, $invoiceType),
            'diaVenctoBoleto' => (int) data_get($this->service, 'dueDate', 0),
            'tipoFatura' => $invoiceType === 'default' ? '' : $invoiceType,
            'status' => data_get($this->service, 'status', ''),
            'ddd' => $this->getAreaCode(),
            'numTelefone' => $this->getPhoneNumber(),
            'fidelizado' => $this->getFidelity(),
            'valorDescontoFidelizacao' => (float) data_get($this->service, 'promotion.price', 0.00),
            'iccid' => data_get($this->service, 'iccid', '')
        ];
    }

    /** @return mixed[] */
    private function deviceProperties(): array
    {
        $deviceImei = data_get($this->service, 'imei');
        return [
            'sku' => (int) data_get($this->service, 'device.sku', 0),
            'imei' => empty($deviceImei) ? null : (int) $deviceImei,
            'modelo' => data_get($this->service, 'device.model', ''),
            'descricao' => data_get($this->service, 'device.label', ''),
            'precoSemDesconto' => (float) data_get($this->service, 'device.priceWithout', 0.00),
            'precoComDesconto' => (float) data_get($this->service, 'device.priceWith', 0.00)
        ];
    }

    /** @return mixed[] */
    private function customerProperties(): array
    {
        $customer  = data_get($this->service, 'customer', []);
        $mainPhone = data_get($customer, 'mainPhone', '');
        return [
            'cpf' => data_get($customer, 'cpf', ''),
            'nomeCliente' => data_get($customer, 'firstName', '') .' '. data_get($customer, 'lastName', ''),
            'ddd' => (int) (is_integer($mainPhone) ? substr(((string) $mainPhone), 3, 2) : substr($mainPhone, 3, 2)),
            'numTelefone' => (string) substr($mainPhone, 5),
            'dataNascimento' => data_get($customer, 'birthday', now()->format('Y-m-d')),
            'email' => data_get($customer, 'email', ''),
        ];
    }

    /** @return mixed[] */
    private function addressProperties(): array
    {
        $customer = data_get($this->service, 'customer', []);
        $zipCode  = data_get($customer, 'zipCode', 0);
        return [
            'cep' => (int) (is_string($zipCode) ? str_replace(['.', '-'], ['', ''], $zipCode) : $zipCode),
            'logradouro' => data_get($customer, 'local', ''),
            'numero' => (int) data_get($customer, 'number', 0),
            'bairro' => data_get($customer, 'neighborhood', ''),
            'cidade' => data_get($customer, 'city', ''),
            'estado' => data_get($customer, 'state', ''),
        ];
    }

    /** @return mixed[] */
    private function salesmanProperties(): array
    {
        $sale        = $this->service->sale;
        $pointOfSale = $sale->pointOfSale;

        return  [
            'codigoFilial' => (int) data_get($pointOfSale, 'slug', 0),
            'dddFilial' => (int) data_get($pointOfSale, 'areaCode', 0),
            'estadoFilial' => data_get($pointOfSale, 'state', ''),
            'cpf' => (string) data_get($sale->user, 'cpf', ''),
            'canal' => (string) ($sale->channel? strtolower($sale->channel) : '')
        ];
    }

    /** @return mixed[] */
    private function campaignProperties(): array
    {
        return [
            'campanhaTriangulacao' => data_get($this->service, 'discount.title', ''),
            'descontoTriangulacao' => (float) data_get($this->service, 'discount.discount', 0.00),
        ];
    }

    /** @return mixed[] */
    private function automaticDebitProperties(): array
    {
        return [
            'idBanco' => data_get($this->service, 'bankId', 0),
            'agencia' => data_get($this->service, 'agency', 0),
            'digitoAgencia' => 0,
            'conta' => data_get($this->service, 'checkingAccount', 0),
            'digitoConta' => 0
        ];
    }

    private function getAreaCode(): int
    {
        $areaCode = data_get($this->service, 'areaCode');
        if ($areaCode !== null) {
            return (int) $areaCode;
        }

        $msisdn = data_get($this->service, 'msisdn');
        if ($msisdn !== null) {
            $msisdn = is_integer($msisdn) ? (string) $msisdn : $msisdn;
            return (int) substr($msisdn, 0, 2);
        }

        return 0;
    }

    private function getPhoneNumber(): string
    {
        $number = data_get($this->service, 'msisdn', '')
            ?? data_get($this->service, 'portedNumber', '');

        $number = is_integer($number) ? (string) $number : $number;

        if (strlen($number) > 11) {
            return (string) substr($number, -11);
        }

        return (string) $number;
    }

    private function getFidelity(): string
    {
        return preg_match(
            '/fidel*/i',
            data_get($this->service, 'promotion.label', '')
        )
            ? 'S'
            : 'N';
    }
}
