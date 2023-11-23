<?php

use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Enumerators\UserStatus;
use Illuminate\Database\Seeder;

class ThirdPartyAccessSchumannSeeder extends Seeder
{
    use ThirdPartyAccess;

    const NETWORK     = NetworkEnum::RIACHUELO;

    const ROLE_NAME   = "Integrador Schumann";
    const ROLE_SLUG   = "integrador-schumann";
    const ROLE_PARENT = "gerente-rede-schumann";

    const FIRST_NAME  = "Integrador";
    const LAST_NAME   = "Schumann";
    const EMAIL       = "integrador@schumann.com.br";
    const USER_CPF    = "00000005150";
    const STATUS_CODE = UserStatus::ACTIVE;
}