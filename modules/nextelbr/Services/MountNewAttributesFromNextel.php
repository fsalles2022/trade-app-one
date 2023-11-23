<?php

namespace NextelBR\Services;

use Illuminate\Support\Facades\Cache;
use NextelBR\Enumerators\NextelBRCaches;
use NextelBR\Enumerators\NextelInvoiceTypes;
use NextelBR\Exceptions\EligibilityNotFound;
use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Services\MountNewAttributesService;

class MountNewAttributesFromNextel implements MountNewAttributesService
{
    public function getAttributes(array $service): array
    {
        $customer    = data_get($service, 'customer');
        $cpf         = data_get($customer, 'cpf');
        $eligibility = Cache::get(NextelBRCaches::ELIGIBILITY . $cpf);

        if ($eligibility) {
            $operatorIdentifiers['protocolo']    = data_get($eligibility, 'protocolo');
            $operatorIdentifiers['numeroPedido'] = data_get($eligibility, 'numeroPedido');

            $customer['score'] = data_get($eligibility, 'score');
            $plans             = data_get($eligibility, 'plans', collect());
            $planChoiced       = $plans->where('product', data_get($service, 'product'))->where('offer', data_get($service, 'offer'))->first();
            $label             = data_get($planChoiced, 'label');
            $price             = data_get($planChoiced, 'price');
            $adhesionValue     = data_get($planChoiced, 'adhesionValue');

            if ($portedNumber = data_get($service, 'portedNumber')) {
                $areaCode = MsisdnHelper::getAreaCode($portedNumber);
            }

            if (data_get($service, 'operation') == Operations::NEXTEL_CONTROLE_CARTAO) {
                $invoiceType = NextelInvoiceTypes::CARTAO_DE_CREDITO;
            } elseif ($directDebit = data_get($service, 'directDebit')) {
                $checkingAccount                  = data_get($directDebit, 'checkingAccount', '');
                $directDebit['checkingAccountDv'] = substr($checkingAccount, -1);
                $directDebit['checkingAccount']   = substr($checkingAccount, 0, strlen($checkingAccount) - 1);
                $invoiceType                      = NextelInvoiceTypes::DEBITO_AUTOMATICO_LIST;
            } else {
                $invoiceType = data_get($service, 'invoiceType');
            }

            $newAttributes = compact(
                'customer',
                'operatorIdentifiers',
                'label',
                'areaCode',
                'invoiceType',
                'directDebit',
                'price',
                'adhesionValue'
            );
            return array_filter($newAttributes);
        } else {
            throw new EligibilityNotFound();
        }
    }
}
