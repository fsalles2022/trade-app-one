<?php


namespace Outsourced\Cea\GiftCardConnection;

use Outsourced\Cea\Adapters\Request\CeaRequestAdapter;
use Outsourced\Cea\Components\CeaGiftCardActivationResponse;

class CeaConnection
{
    public const GIFT_CARD_ACTIVATE = 'ativar';

    protected $client;

    public function __construct(CeaSoapClient $ceaClient)
    {
        $this->client = $ceaClient;
    }

    public function activateGiftCard(string $cardNumber, float $rechargeValue, int $partner, array $customer): CeaGiftCardActivationResponse
    {
        $xmlParameters = $this->xmlAdapter($cardNumber, $rechargeValue, $partner, $customer);
        $response      = $this->client->execute(self::GIFT_CARD_ACTIVATE, $xmlParameters);
        return new CeaGiftCardActivationResponse($response);
    }

    private function xmlAdapter(string $cardNumber, float $rechargeValue, int $partner, array $customer): \stdClass
    {
        $requestAdapter = new CeaRequestAdapter();

        return $requestAdapter
            ->setCardNumber($cardNumber)
            ->setChargeValue($rechargeValue)
            ->setPartnerIdentifier($partner)
            ->setClient($customer)
            ->adapt();
    }
}
