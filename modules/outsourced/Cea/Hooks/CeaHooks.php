<?php

namespace Outsourced\Cea\Hooks;

use Outsourced\Cea\Exceptions\CeaExceptions;
use Outsourced\Cea\GiftCardConnection\CeaConnection;
use Outsourced\Cea\Models\CeaGiftCard;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Domain\Services\NetworkHooks\NetworkHook;

class CeaHooks implements NetworkHook
{
    public const PARTNER_TRADE_IN      = 1080;
    public const PARTNER_TRIANGULATION = 1070;

    protected $giftCardConnection;
    protected $saleRepository;

    public function __construct(CeaConnection $giftCardConnection, SaleRepository $saleRepository)
    {
        $this->giftCardConnection = $giftCardConnection;
        $this->saleRepository     = $saleRepository;
    }

    public function execute(Service $service, array $options = []): ?Service
    {
        if ($service->isNotActivated()) {
            return null;
        }

        if ($service->isTradeIn()) {
            $partner = self::PARTNER_TRADE_IN;
            $charge  = data_get($service, 'price');

//            return $this->activateGiftCard($service, $charge, $partner);
        }

        if ($service->isTriangulation()) {
            $partner = self::PARTNER_TRIANGULATION;
            $charge  = data_get($service, 'discount.discount');

//            return $this->activateGiftCard($service, $charge, $partner);
        }

        return null;
    }

    public function activateGiftCard(Service $service, float $charge, int $partner): Service
    {
        $card = CeaGiftCard::where('partner', $partner)->first();
        throw_if($card === null, CeaExceptions::cardsUnavailable());

        $card->update([
            'value'     => $charge,
            'partner'   => $partner,
            'reference' => $service->serviceTransaction
        ]);

        $card->delete();

        $response = $this->giftCardConnection->activateGiftCard($card->code, $charge, $partner, $service->customer);

        if ($response->isActivated()) {
            $card->update(['outsourcedId' => $response->getIDTransacao()]);

            return $this->saleRepository->updateService($service, [
                'register' => [
                    'card' => $card->code
                ]
            ]);
        }

        return $service;
    }
}
