<?php

namespace TradeAppOne\Domain\Exportables;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use TradeAppOne\Domain\Enumerators\Portfolio;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Sale;

class SalesExport implements FromCollection, WithHeadings
{
    use Exportable;

    public function collection()
    {
        $pointsOfSale = Auth::user()->pointsOfSale()->select('cnpj')->get()->pluck('cnpj')->toArray();
        $sales        = Sale::whereIn('pointOfSale.cnpj', $pointsOfSale)
            ->whereIn('services.status', [
                ServiceStatus::APPROVED,
                ServiceStatus::CANCELED,
                ServiceStatus::ACCEPTED,
                ServiceStatus::REJECTED,
            ])
            ->get()
            ->toArray();
        $toExport     = new Collection();
        foreach ($sales as $sale) {
            $record['createdAt'] = $sale['createdAt'];
            $record['network']   = $sale['pointOfSale']['network']['slug'];
            $record['slug']      = $sale['pointOfSale']['slug'];
            $record['user.cpf']  = $sale['user']['cpf'];
            $record['user.nome'] = "{$sale['user']['firstName']} {$sale['user']['lastName']}";
            foreach ($sale['services'] as $service) {
                $record['serviceTransaction'] = $service['serviceTransaction'];
                $record['customer.cpf']       = $service['customer']['cpf'] . '';
                $record['customer.nome']      = "{$service['customer']['firstName']} {$service['customer']['lastName']}";
                $record['operator']           = $service['operator'];
                $record['mode']               = trans('status.' . $service['mode']) ?? '';
                $record['operation']          = $service['operation'];
                $record['product']            = Portfolio::PLANS[$service['product']] ?? $service['product'];
                $record['iccid']              = $service['iccid'] ?? '';
                $record['msisdn']             = $service['msisdn'] ?? '';
                $record['fatura']             = $service['invoiceType'] ?? '';
                $record['status']             = trans('status.' . $service['status']) ?? '';
                $toExport->push($record);
            }
        }
        return $toExport;
    }

    public function headings(): array
    {
        return [
            'data',
            'rede',
            'pdv',
            'vendedor.cpf',
            'vendedor.nome',
            'transacaoDeServico',
            'cliente.cpf',
            'cliente.nome',
            'operadora',
            'modalidade',
            'tipo',
            'plano',
            'iccid',
            'msisdn',
            'fatura',
            'status',
        ];
    }
}
