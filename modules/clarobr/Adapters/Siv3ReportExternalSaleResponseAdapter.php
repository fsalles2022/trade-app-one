<?php

declare(strict_types=1);

namespace ClaroBR\Adapters;

use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;
use TradeAppOne\Domain\HttpClients\Responseable;

class Siv3ReportExternalSaleResponseAdapter extends ResponseAdapterAbstract
{
    public function __construct(Responseable $originalResponse)
    {
        parent::__construct($originalResponse);
        $this->adapterResponse();
        $this->status = $originalResponse->getStatus();
    }

    protected function adapterResponse(): void
    {
        $sales         = [];
        $externalSales = $this->originalResponse->get('data');

        foreach ($externalSales as $externalSale) {
            $sales[] = [
                'mode' => data_get($externalSale, 'mode', ''),
                'areaCode' => data_get($externalSale, 'areaCode', ''),
                'msisdn' => data_get($externalSale, 'msisdn', ''),
                'iccid' => data_get($externalSale, 'iccid', ''),
                'customerCpf' => data_get($externalSale, 'customerCpf', ''),
                'customerEmail' => data_get($externalSale, 'customerEmail', ''),
                'salesmanCpf' => data_get($externalSale, 'salesmanCpf', ''),
                'salesmanName' => data_get($externalSale, 'salesmanName', ''),
                'salesmanAreaCode' => data_get($externalSale, 'salesmanAreaCode', ''),
                'pointOfSaleCode' => data_get($externalSale, 'pointOfSaleCode'),
                'pointOfSaleHierarchyId' => data_get($externalSale, 'pointOfSaleHierarchyId'),
                'pointOfSaleHierarchyName' => data_get($externalSale, 'pointOfSaleHierarchyName'),
                'pointOfSaleName' => data_get($externalSale, 'pointOfSaleName'),
                'networkSlug' => data_get($externalSale, 'networkSlug'),
                'createdAt' => data_get($externalSale, 'createdAt')
            ];
        }

        $this->adapted = $sales;
    }
}
