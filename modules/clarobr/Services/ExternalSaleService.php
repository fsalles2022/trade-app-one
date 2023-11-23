<?php

declare(strict_types=1);

namespace ClaroBR\Services;

use ClaroBR\Adapters\Siv3CheckExternalSaleExistsResponseAdapter;
use ClaroBR\Adapters\Siv3CreateExternalSaleResponseAdapter;
use ClaroBR\Adapters\Siv3ExternalSaleRequestAdapter;
use ClaroBR\Connection\Siv3Connection;
use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;

class ExternalSaleService
{
    /** @var Siv3Connection */
    private $sivConnection;

    public function __construct(Siv3Connection $sivConnection)
    {
        $this->sivConnection = $sivConnection;
    }

    /**
     * @param mixed[] $dataCustomer
     * @return mixed[]
     */
    public function checkExternalSaleExist(array $dataCustomer): array
    {
        $areaCode = data_get($dataCustomer, 'areaCode', '') ?? MsisdnHelper::getAreaCode(data_get($dataCustomer, 'msisdn'));
        $msisdn   = substr(data_get($dataCustomer, 'msisdn', ''), 2, MsisdnHelper::MIN_LENGTH);

        $dataCustomer['areaCode'] = $areaCode;
        $dataCustomer['msisdn']   = $msisdn;

        $hydrate = (new Siv3CheckExternalSaleExistsResponseAdapter($this->sivConnection->checkSale($dataCustomer)));
        return [
            'response' => $hydrate->getAdapted(),
            'statusCode' => $hydrate->getStatus()
        ];
    }

    /**
     * @param mixed[] $saleExternal
     * @return mixed[]
     */
    public function insertExternalSale(array $saleExternal): array
    {
        $response = $this->sivConnection->createSale(Siv3ExternalSaleRequestAdapter::adapt($saleExternal, Auth::user()));

        $hydrate = (new Siv3CreateExternalSaleResponseAdapter($response));

        return [
            'response' => $hydrate->getAdapted(),
            'statusCode' => $hydrate->getStatus()
        ];
    }
}
