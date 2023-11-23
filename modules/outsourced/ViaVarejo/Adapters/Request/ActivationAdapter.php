<?php


namespace Outsourced\ViaVarejo\Adapters\Request;

use Outsourced\ViaVarejo\DataTransferObjects\ViaVarejoBase;
use Outsourced\ViaVarejo\Enumerators\ViaVarejoInvoiceType;
use Outsourced\ViaVarejo\Enumerators\ViaVarejoModes;
use Outsourced\ViaVarejo\Enumerators\ViaVarejoOperators;
use Outsourced\ViaVarejo\Enumerators\ViaVarejoPaymentType;
use Outsourced\ViaVarejo\Enumerators\ViaVarejoPlans;
use Outsourced\ViaVarejo\Enumerators\ViaVarejoStatus;

class ActivationAdapter extends ViaVarejoBase implements PayloadAdapterInterface
{
    public function plan(): array
    {
        $service  = $this->service;
        $operator = $service->operator;

        return [
            'id' => (int) filter_var($service->product, FILTER_SANITIZE_NUMBER_INT),
            'valorTotalFinal' => $service->price ?: 0,
            'idOperadora' => ViaVarejoOperators::get($operator),
            'operacao' => ViaVarejoModes::get($service->mode),
            'tipoPagamento' => ViaVarejoPaymentType::get($operator, (string) $service->invoiceType),
            'diaVenctoBoleto' => (int) data_get($service, 'dueDate', 0),
            'tipoFatura' => ViaVarejoInvoiceType::get((string) $service->invoiceType),
            'tipoPlano' => ViaVarejoPlans::get($operator, $service->operation),
            'status' => ViaVarejoStatus::get($service->status),
            'ddd' => $this->getAreaCode(),
            'numTelefone' => $this->getPhoneNumber($service->msisdn ?? $service->portedNumber),
            'fidelizado' => ViaVarejoPlans::getFidelity($service->promotion),
            'valorDescontoFidelizacao' => data_get($service->promotion, 'price', 0),
            'iccid' => $service->iccid
        ];
    }

    public function toArray(): array
    {
        return ['data' => [[
            'ftm'      => $this->getFTM(),
            'plano'    => $this->plan(),
            'cliente'  => $this->getCustomer(),
            'endereco' => $this->getAddress(),
            'vendedor' => $this->getSalesMan(),
            'campanha' => $this->getCampaign(),
            'dadosBancarios' => $this->getAutomaticDebit()
        ]]];
    }
}
