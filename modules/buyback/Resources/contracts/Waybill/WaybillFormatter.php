<?php

declare(strict_types=1);

namespace Buyback\Resources\contracts\Waybill;

use Buyback\Enumerators\WaybillCarriers;
use Buyback\Services\Waybill;

class WaybillFormatter
{
    private $waybill;

    public function __construct(Waybill $waybill)
    {
        $this->waybill = $waybill;
    }

    public function format(): string
    {
        $pointOfSale = $this->waybill->pointOfSale;
    
        $info  = $this->createRow([ "DATA" => $this->BrazilianDate() ]);
        $info .= $this->createRow([ "LOJA" => $pointOfSale->slug ]);

        $info .= $this->createRow([
        "BAIRRO" => $pointOfSale->neighborhood,
        "CIDADE" => $pointOfSale->city
        ]);

        $info .= $this->createRow([
        "ENDEREÇO" => $pointOfSale->local,
        "NÚMERO" => $pointOfSale->number,
        "COMPLEMENTO" => $pointOfSale->complement
        ]);

        $info .= $this->createRow([ "TELEFONE" => $pointOfSale->telephone ]);
        $info .= $this->createRow([ "IDENTIFICADOR DO ROMANEIO" => $this->waybill->id ]);

        return $info;
    }
  
    private function createLine($label, $value): string
    {
        if ($value) {
            return "<p>{$label}: <strong>{$value}</strong></p>";
        }
        return "";
    }

    private function createRow($rowLines): string
    {
        $wrapper = "<div class='block'>";

        foreach ($rowLines as $label => $value) {
            $wrapper .= $this->createLine($label, $value);
        }

        $wrapper .= "</div>";

        return $wrapper;
    }

    private function brazilianDate(): string
    {
        return $this->waybill->date->format('d/m/Y');
    }

    /**
     * @return string[]
     */
    public function getAvailableColumns(): array
    {
        return [
            'ID',
            'IMEI',
            'MODELO',
            'AUDITOR DO ROMANEIO',
            'DATA DO ROMANEIO',
            'ASSINATURA',
        ];
    }

    public function getCarrier(): string
    {
        return data_get(
            WaybillCarriers::CARRIER_BY_OPERATION,
            $this->waybill->services->first()->operation,
            ''
        );
    }
}
