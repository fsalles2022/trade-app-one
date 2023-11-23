<?php

use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Enumerators\UserStatus;
use Illuminate\Database\Seeder;

class ThirdPartyAccessCeaSeeder extends Seeder
{
    use ThirdPartyAccess;

    const NETWORK     = NetworkEnum::CEA;

    const ROLE_NAME   = "Integrador CEA";
    const ROLE_SLUG   = "integrador-cea";
    const ROLE_PARENT = "gerente-rede";

    const FIRST_NAME  = "Integrador";
    const LAST_NAME   = "CEA";
    const EMAIL       = "cea@cea.com.br";
    const USER_CPF    = "00000002054";
    const STATUS_CODE = UserStatus::ACTIVE;
}