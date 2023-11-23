<?php

declare(strict_types=1);

namespace Outsourced\GPA\Adapters\Request;

use Outsourced\GPA\DataTransferObjects\GpaDTO;

class ActivationAdapter extends GpaDTO implements PayloadAdapterInterface
{
    /*** @return mixed[] */
    protected static function plan(): array
    {
        $service = self::$service;

        return [
            'id' => $service->product,
            'valorTotalFinal' => $service->price ?: 0,
            'operadora' => $service->operator ?? '',
            'operacao' => $service->operation ?? '',
            'tipoPagamento' => $service->invoiceType ?? '',
            'diaVenctoBoleto' => $service->dueDate ?? '',
            'tipoFatura' => $service->billType ?? '',
            'status' => $service->status ?? '',
            'ddd' => self::getAreaCode(),
            'numTelefone' => (int) ($service->msisdn ?? $service->portedNumber),
            'fidelizado' => self::getFidelity($service->promotion),
            'valorDescontoFidelizacao' => data_get($service->promotion, 'price', 0),
        ];
    }

    /*** @return mixed[] */
    public function toArray(): array
    {
        return [
            'info'     => self::getInfo(),
            'plano'    => self::plan(),
            'aparelho' => self::getDevice(),
            'cliente'  => self::getCustomer(),
            'endereco' => self::getAddress(),
            'vendedor' => self::getSalesMan(),
            'campanha' => self::getCampaign(),
            'dadosBancariosDebitoAutomatico' => self::getAutomaticDebit()
        ];
    }
}
