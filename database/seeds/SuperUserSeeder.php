<?php

use Illuminate\Database\Seeder;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\User;

class SuperUserSeeder extends Seeder
{

    public function run()
    {
        $tradeUpGroup = Network::firstOrNew(['cnpj' => '22696923000162']);

        $tradeUpGroup->fill([
            "slug"              => "tradeup-group",
            "label"             => "TradeUp Group",
            "cnpj"              => "22696923000162",
            "tradingName"       => "Trade Up Serviço de Apoio Administrativo e Comércio de Equipamentos de Telefonia e Comunicação LTDA",
            "companyName"       => "TradeUp Group",
            "telephone"         => "94232-1314",
            "preferences"       => '[{"initialPage" : "painel/activation"}]',
            "zipCode"           => "06454-000",
            "local"             => "Alameda Rio Negro",
            "neighborhood"      => "Alphaville Industrial",
            "state"             => "SP",
            "number"            => 503,
            "city"              => "Barueri",
            "complement"        => "Escritório 915",
            "availableServices" => json_encode([]),
        ])->save();

        $pointOfSale = PointOfSale::firstOrNew(['cnpj'=> '22696923000162']);
        $pointOfSale->fill([
            "label"                  => "Matriz - Rio Negro",
            "slug"                   => "matriz-rio-negro",
            "tradingName"            => "Trade Up Serviço de Apoio Administrativo e Comércio de Equipamentos de Telefonia e Comunicação LTDA",
            "companyName"            => "TradeUp Group",
            "cnpj"                   => "22696923000162",
            "areaCode"               => "11",
            "providerIdentifiers"    => '{}',
            "zipCode"                => "06454-000",
            "local"                  => "Alameda Rio Negro",
            "neighborhood"           => "Alphaville Industrial",
            "state"                  => "SP",
            "number"                 => 503,
            "city"                   => "Barueri",
            "complement"             => "Escritório 915"
        ]);
        $pointOfSale->network()->associate($tradeUpGroup)->save();

        $admin = Role::firstOrNew(['slug' => 'administrator']);
        $admin->fill([
            "name"        => "Administrator",
            "slug"        => "administrator",
            "level"       => 0,
        ]);
        $admin->network()->associate($tradeUpGroup)->save();

        $userAdmin = User::firstOrNew(['cpf' => '24781956076']);
        $userAdmin->fill([
            'firstName'            => 'Admin',
            'lastName'             => 'Trade UP',
            'email'                => 'desenvolvedores@tradeupgroup.com.br',
            'cpf'                  => '24781956076',
            "areaCode"             => "11",
            "activationStatusCode" => \TradeAppOne\Domain\Enumerators\UserStatus::ACTIVE,
            "password"             => bcrypt('24781956076'),
            "roleId"               => $admin->id
        ]);

        $userAdmin->role()->associate($admin)->save();

        if (! $userAdmin->pointsOfSale()->first()) {
            $userAdmin->pointsOfSale()->attach($pointOfSale);
        }
    }
}
