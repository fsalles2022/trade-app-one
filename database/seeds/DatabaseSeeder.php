<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use TradeAppOne\Domain\Enumerators\Environments;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        if (Environments::PRODUCTION != App::environment()) {
            $this->call(IntegrationSeeder::class);
            $this->call(ResetUsersPasswordToDefaultSeeder::class);
            $this->call(ActivateUsersSeeder::class);
            $this->call(ClaroAgenteAutorizadoSeeder::class);
            $this->call(BuybackPartnerManagementSeeder::class);
            $this->call(DevicesSeeder::class);
            $this->call(DiscountsSeeder::class);
            $this->call(PermissionSeeder::class);
            $this->call(ClienteTrialTradeAppOne::class);
            $this->call(ThirdPartyAccessRiachueloSeeder::class);
            $this->call(ThirdPartyAccessCeaSeeder::class);
            $this->call(ThirdPartyAccessLebesSeeder::class);
        }
    }
}
