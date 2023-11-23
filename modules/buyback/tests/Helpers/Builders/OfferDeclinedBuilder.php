<?php

namespace Buyback\Tests\Helpers\Builders;

use Buyback\Models\OfferDeclined;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;

class OfferDeclinedBuilder
{
    private $user;

    public function withUser(User $user): OfferDeclinedBuilder
    {
        $this->user = $user;
        return $this;
    }

    public function build(): OfferDeclined
    {
        $deviceEntity      = (new DeviceBuilder())->build();
        $questions         = (new QuestionBuilder())->build();
        $userEntity        = $this->user ?? (new UserBuilder())->build();
        $pointOfSaleEntity = $userEntity->pointsOfSale;
        $questions         = array_except($questions->toArray(), 'network');

        $offerDeclined              = factory(OfferDeclined::class)->make();
        $offerDeclined->device      = array_merge($deviceEntity->toArray(), $offerDeclined->device);
        $offerDeclined->questions   = [array_merge($questions, ['answer' => true])];
        $offerDeclined->pointOfSale = $pointOfSaleEntity->toArray()[0];
        $offerDeclined->user        = $userEntity->toArray();

        $offerDeclined->save();
        return $offerDeclined;
    }
}
