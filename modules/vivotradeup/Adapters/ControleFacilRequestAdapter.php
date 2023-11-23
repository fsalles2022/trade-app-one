<?php


namespace VivoTradeUp\Adapters;

use Carbon\Carbon;
use TradeAppOne\Domain\Components\Helpers\Blowfish\BlowfishHelper;
use TradeAppOne\Domain\Models\Collections\Service;

class ControleFacilRequestAdapter
{
    private const CHANNEL = 'varejo-web-tradeup';

    public static function adapt(Service $service, string $externalId): string
    {
        $sale          = $service->sale;
        $date          = Carbon::now()->format('dmY');
        $sharedKey     = config('vivotradeup.VIVO_M4U_TRADEUP_API_KEY'). $date;
        $sharedKeyOnly = config('vivotradeup.VIVO_M4U_TRADEUP_API_KEY');
        $kInput        = str_pad(random_int(1, 9999999999), 32, '0');

        $completeKey = $sharedKeyOnly . $kInput;
        $key         = $completeKey;
        if (strlen($completeKey) > 56) {
            $key = substr($completeKey, 0, 56);
        }

        $cnpjByVivoCommcenter = data_get($sale, 'pointOfSale.providerIdentifiers.VIVO_COMMCENTER_CNPJ', null);

        $query = [
            'k' => null,
            'canal' => self::CHANNEL,
            'msisdn' => data_get($service, 'msisdn', null),
            'cpf' => data_get($service, 'customer.cpf', null),
            'plano' => data_get($service, 'planSlug', null),
            'cpfPr' => data_get($sale, 'user.cpf', null),
            'cnpjPr' => !empty($cnpjByVivoCommcenter) ? $cnpjByVivoCommcenter : data_get($sale, 'pointOfSale.cnpj', null),
            'externalId' => $externalId
        ];

        $toMap = function ($item) use ($key) {
            return base64_encode(
                BlowfishHelper::encrypt(
                    $item,
                    $key,
                    BlowfishHelper::BLOWFISH_MODE_EBC,
                    BlowfishHelper::BLOWFISH_PADDING_ZERO
                )
            );
        };

        $queryEncrypted = array_map($toMap, $query);

        $kCrypt              = BlowfishHelper::encrypt(
            $kInput,
            $sharedKey,
            BlowfishHelper::BLOWFISH_MODE_EBC,
            BlowfishHelper::BLOWFISH_PADDING_ZERO
        );
        $kValue              = base64_encode($kCrypt);
        $queryEncrypted['k'] = $kValue;

        $url         = config('vivotradeup.VIVO_M4U_TRADEUP_API');
        $queryString = http_build_query($queryEncrypted, '', '&');
        return $url . '?' . $queryString;
    }
}
