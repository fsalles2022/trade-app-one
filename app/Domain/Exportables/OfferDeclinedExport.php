<?php

namespace TradeAppOne\Domain\Exportables;

use Buyback\Models\OfferDeclined;
use Illuminate\Support\Collection;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;

class OfferDeclinedExport
{
    protected $offersDeclined;

    public function __construct(Collection $offersDeclined)
    {
        $this->offersDeclined = $offersDeclined;
    }

    public function export()
    {
        $lines = [];
        array_push($lines, $this->headings());
        foreach ($this->offersDeclined as $offersDeclined) {
            array_push($lines, $this->collection($offersDeclined));
        }
        return CsvHelper::arrayToCsv($lines);
    }

    public function headings(): array
    {
        return [
            'PDV',
            'Operadora',
            'Operacao',
            'Nome Vendedor',
            'CPF Vendedor',
            'Nome Cliente',
            'Email Cliente',
            'Telefone Cliente',
            'Motivo',
            'Label Aparelho',
            'Modelo Aparelho',
            'Marca Aparelho',
            'Capacidade Aparelho',
            'Preco Aparelho',
            'Nota Aparelho',
            'Imei Aparelho',
            'Data de criacao'
        ];
    }

    public function collection(OfferDeclined $offerDeclined)
    {
        $date = data_get($offerDeclined, 'createdAt');

        return [
            data_get($offerDeclined, 'pointOfSale.slug'),
            data_get($offerDeclined, 'operator'),
            data_get($offerDeclined, 'operation'),
            data_get($offerDeclined, 'user.firstName') . ' ' . data_get($offerDeclined, 'user.lastName'),
            data_get($offerDeclined, 'user.cpf'),
            data_get($offerDeclined, 'customer.fullName'),
            data_get($offerDeclined, 'customer.email'),
            data_get($offerDeclined, 'customer.mainPhone'),
            data_get($offerDeclined, 'reason'),
            data_get($offerDeclined, 'device.label'),
            data_get($offerDeclined, 'device.model'),
            data_get($offerDeclined, 'device.brand'),
            data_get($offerDeclined, 'device.storage'),
            data_get($offerDeclined, 'device.price'),
            data_get($offerDeclined, 'device.note'),
            data_get($offerDeclined, 'device.imei'),
            $date->format('d-m-Y H:i')
        ];
    }
}
