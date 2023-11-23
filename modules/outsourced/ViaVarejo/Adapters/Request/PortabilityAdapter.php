<?php


namespace Outsourced\ViaVarejo\Adapters\Request;

use Outsourced\ViaVarejo\DataTransferObjects\ViaVarejoBase;
use Outsourced\ViaVarejo\Enumerators\ViaVarejoModes;
use Outsourced\ViaVarejo\Enumerators\ViaVarejoOperators;
use Outsourced\ViaVarejo\Enumerators\ViaVarejoPaymentType;
use Outsourced\ViaVarejo\Enumerators\ViaVarejoPlans;
use Outsourced\ViaVarejo\Enumerators\ViaVarejoStatus;

class PortabilityAdapter extends ViaVarejoBase implements PayloadAdapterInterface
{
    public function plan(): array
    {
        $service      = $this->service;
        $operator     = $this->service->operator;
        $fromOperator =  data_get($this->service, 'fromOperator', []);

        return [
            'id' => (int) filter_var($service->product, FILTER_SANITIZE_NUMBER_INT),
            'valorTotalFinal' => $service->price ?: 0,
            'idOperadora' => ViaVarejoOperators::get($operator),
            'operacao' => ViaVarejoModes::get($service->mode),
            'tipoPagamento' => ViaVarejoPaymentType::get($operator, $service->invoiceType),
            'diaVenctoBoleto' => (int) data_get($service, 'dueDate', 0),
            'tipoPlano' => ViaVarejoPlans::get($operator, $service->operation),
            'iccid' => $service->iccid,
            'status' => ViaVarejoStatus::get($service->status),
            'ddd' => $this->getAreaCode(),
            'numTelefone' => $this->getPhoneNumber($service->msisdn ?? $service->portedNumber),
            'fidelizado' => ViaVarejoPlans::getFidelity($service->promotion),
            'valorDescontoFidelizacao' => data_get($service->promotion, 'price', 0),
            'idOperadoraOrigem' =>  ViaVarejoOperators::getPortabilityOperator($fromOperator),
            'outraOperadoraOrigem' => data_get($fromOperator, 'label')

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
