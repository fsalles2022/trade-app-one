<?php

namespace Buyback\Tests\Helpers\Builders;

use Buyback\Services\Waybill;
use Buyback\Tests\Helpers\TradeInServices;
use Carbon\Carbon;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Facades\Uniqid;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;

class WaybillBuilder
{
    private $alreadyPrinted    = false;
    private $withDrawn         = false;
    private $quantity          = 1;
    private $selectedOperation = Operations::SALDAO_INFORMATICA;
    private $pointOfSale;
    private $user;

    public function alreadyPrinted(): WaybillBuilder
    {
        $this->alreadyPrinted = true;
        return $this;
    }

    public function withDrawn(): WaybillBuilder
    {
        $this->withDrawn = true;
        return $this;
    }

    public function quantityOfServices(int $quantity = 1): WaybillBuilder
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function withOperation(string $operation): WaybillBuilder
    {
        $this->selectedOperation = $operation;
        return $this;
    }

    public function withPointOfSale(PointOfSale $pointOfSale): WaybillBuilder
    {
        $this->pointOfSale = $pointOfSale;
        return $this;
    }

    public function withUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    public function build()
    {
        $pointOfSale = $this->pointOfSale ?? (new PointOfSaleBuilder())->build();
        $user        = $this->user ?? (new UserBuilder())->build();

        $id             = Uniqid::generate();
        $listOfServices = $this->getServices($id);

        (new SaleBuilder())
            ->withUser($user)
            ->withPointOfSale($pointOfSale)
            ->withServices($listOfServices)
            ->build();

        $waybill = new Waybill(
            $pointOfSale,
            collect($listOfServices),
            Carbon::now()
        );

        if ($this->alreadyPrinted) {
            $waybill->id = $id;
        }

        return $waybill;
    }

    private function getServices($id): array
    {
        $listOfServices = [];

        foreach (range(1, $this->quantity) as $index) {
            $buyback = $this->createBasedOnSelected();

            if ($this->alreadyPrinted) {
                $buyback['waybill'] = [
                    'printedAt'     => Carbon::now(),
                    'id'            => $id
                ];
            }

            if ($this->withDrawn) {
                $buyback['waybill'] = array_merge(
                    $buyback['waybill'] ?? [],
                    [
                        'withdrawnDate' => now()->toIso8601String(),
                        'withdrawn'     => true,
                    ]
                );
            }


            array_push($listOfServices, $buyback);
        }

        return $listOfServices;
    }

    private function createBasedOnSelected()
    {
        switch ($this->selectedOperation) {
            case Operations::SALDAO_INFORMATICA:
                return TradeInServices::SaldaoInformaticaMobile();
                break;
            case Operations::IPLACE:
                return TradeInServices::IplaceMobile();
                break;
            case Operations::TRADE_NET:
                return TradeInServices::TradeNetMobile();
                break;
        }
    }
}
