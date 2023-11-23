<?php

namespace TradeAppOne\Tests\Helpers\Builders;

use TradeAppOne\Domain\Enumerators\PasswordResetStatus;
use TradeAppOne\Domain\Models\Tables\PasswordReset;
use TradeAppOne\Domain\Models\Tables\User;

class PasswordResetBuilder
{

    private $user;
    private $differentNetworks = false;
    private $status;

    public function withUser(User $user): PasswordResetBuilder
    {
        $this->user = $user;
        return $this;
    }

    public function WithDifferentNetworks(): PasswordResetBuilder
    {
        $this->differentNetworks = true;
        return $this;
    }

    public function withStatus(PasswordResetStatus $status): PasswordResetBuilder
    {
        $this->status = $status;
        return $this;
    }

    public function generatePasswordResetTimes(int $quantity)
    {
        foreach (range(1, $quantity) as $index) {
            $this->build();
        }
    }

    public function build()
    {
        $passwordReset = new PasswordReset($this->getPasswordResetData());
        $passwordReset->save();

        return $passwordReset;
    }

    private function getPasswordResetData()
    {
        if ($this->differentNetworks) {
            $networkInstance = (new NetworkBuilder())->build();
            $defaultUser     = (new UserBuilder())->withNetwork($networkInstance)->build();
        } else {
            $defaultUser = $this->user ?? (new UserBuilder())->build();
        }
        return [
            'userId' => $defaultUser->id,
            'pointsOfSaleId' => $defaultUser->pointsOfSale->first()->id,
            'status' => $status ?? PasswordResetStatus::WAITING
        ];
    }
}
