<?php

namespace Buyback\Exportables\OfferDeclined;

use Buyback\Exportables\AnalyticalReportIndexes as Index;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class OfferDeclinedUnifiedMap
{
    public static function recordsToArray(Collection $offersDeclined): array
    {
        $mapped = $offersDeclined->map(function ($offersDeclined) {
            return self::collection($offersDeclined);
        });

        return $mapped->toArray();
    }

    private static function collection($offer): array
    {
        $map = [];

        $map[Index::DATE]             = Carbon::parse(data_get($offer, 'createdAt', ''))->format('d/m/Y');
        $map[Index::HOUR]             = Carbon::parse(data_get($offer, 'createdAt', ''))->format('H:i');
        $map[Index::SALETRANSACTION]  = '';
        $map[Index::CPF]              = '';
        $map[Index::NAME]             = data_get($offer, 'customer.fullName');
        $map[Index::CITY]             = '';
        $map[Index::LOCAL]            = '';
        $map[Index::NUMBER]           = '';
        $map[Index::COMPLEMENT]       = '';
        $map[Index::IMEI]             = data_get($offer, 'device.imei');
        $map[Index::MODELID]          = data_get($offer, 'device.id');
        $map[Index::MODEL]            = data_get($offer, 'device.model');
        $map[Index::STORAGE]          = data_get($offer, 'device.storage');
        $map[Index::COLOR]            = data_get($offer, 'device.color');
        $map[Index::PRICESALESMAN]    = data_get($offer, 'device.price');
        $map[Index::PRICEAPPRAISER]   = '';
        $map[Index::DATEAPPRAISER]    = '';
        $map[Index::DIFF]             = '';
        $map[Index::CNPJ]             = data_get($offer, 'pointOfSale.cnpj');
        $map[Index::POINTOFSALE_SLUG] = data_get($offer, 'pointOfSale.slug');
        $map[Index::PDV_CITY]         = data_get($offer, 'pointOfSale.city');
        $map[Index::PDV_LOCAL]        = data_get($offer, 'pointOfSale.neighborhood');
        $map[Index::PDV_NUMBER]       = data_get($offer, 'pointOfSale.number');
        $map[Index::PDV_ZIPCODE]      = data_get($offer, 'pointOfSale.zipCode');
        $map[Index::PDV_NETWORK]      = data_get($offer, 'pointOfSale.network.label');
        $map[Index::RECEIVED_AT]      = '';
        $map[Index::PRICE]            = '';
        $map[Index::STATUS]           = '';
        $map[Index::WAYBILL_ID]       = '';
        $map[Index::WAYBILL]          = '';

        $map[Index::TYPE]   = 'DESISTENCIA';
        $map[Index::REASON] = data_get($offer, 'reason');

        return $map;
    }
}
