<?php

use Illuminate\Database\Seeder;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\Permissions;
use TradeAppOne\Domain\Enumerators\UserStatus;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\User;

/**
 * @@property  Role salesman
 */
class ClaroUsuarioHomologador extends Seeder
{
    private $password;
    private $network;
    private $pointOfSale;
    private $salesman;

    //TODO: Seeder de Permissions e Super User devem ser executadas antes.
    public function run()
    {
        $this->password = bcrypt('Cl@ro2019');
        $this->createClaroPartnerNetwork();
    }

    private function createClaroPartnerNetwork()
    {
        $this->network = Network::firstOrNew(['cnpj' => '10002002000013']);
        $this->network->fill([
            "slug"              => "operadora-claro",
            "label"             => "Claro",
            "cnpj"              => "10002002000013",
            "tradingName"       => "Parceiro Claro",
            "companyName"       => "Parceiro Claro",
            "availableServices" => json_encode([
                Operations::LINE_ACTIVATION => [
                    Operations::CLARO => [
                        Operations::CLARO_CONTROLE_BOLETO,
                        Operations::CLARO_CONTROLE_FACIL,
                        Operations::CLARO_POS,
                        Operations::CLARO_BANDA_LARGA,
                        Operations::CLARO_PRE
                    ]
                ]
            ]),
            "preferences"       => json_encode([
                "initialPage" => "painel/activation"
            ]),
        ])->save();
        $this->createClaroPointOfSale();
    }

    public function createClaroPointOfSale()
    {
        $this->pointOfSale = PointOfSale::firstOrNew(['cnpj' => '55478991320002']);
        $this->pointOfSale->fill([
            "label"                  => "Loja Claro",
            "slug"                   => "loja-claro",
            "tradingName"            => "Loca Claro",
            "companyName"            => "Claro",
            "cnpj"                   => "55478991320002",
            "areaCode"               => "11",
            "providerIdentifiers"    => json_encode([
                "claroBR" => "15F0"
            ]),
            "availableServices"      => '{}',
            "zipCode"                => "",
            "local"                  => "",
            "neighborhood"           => "",
            "state"                  => "SP",
            "number"                 => 0,
            "city"                   => "Sao Paulo",
            "complement"             => ""
        ]);
        $this->pointOfSale->network()->associate($this->network)->save();
        $this->createClaroRole();
    }

    public function createClaroRole()
    {
        $admin = Role::query()->where([
            'slug' => 'administrator',
            'name' => 'Administrator',
        ])->first();

        $this->salesman = Role::firstOrNew(['slug' => 'homologador-claro']);

        $permissions = Permission::query()
            ->whereIn('slug', [
                Permissions::DASHBOARD_VIEW,
                Permissions::DASHBOARD_VIEW,
                Permissions::PAINEL_VIEW,
                Permissions::SALE_CREATE,
                Permissions::SALE_FLOW
            ]);

        $this->salesman->fill([
            "name" => "Homologador Claro",
            "slug" => "homologador-claro",
            "parent" => $admin->id,
        ]);

        $this->salesman->network()->associate($this->network)->save();

        foreach ($permissions as $permission) {
            $this->salesman->stringPermissions()->attach($permission);
        }

        $this->salesman->save();
        $this->createClaroPartnerUser();
    }

    private function createClaroPartnerUser(): void
    {
        $claroPartnerCpf = '35215352828';

        $claroPartnerUser = User::firstOrNew(['cpf' => $claroPartnerCpf]);
        $claroPartnerUser->fill([
            'firstName'              => 'Operadora Claro',
            'lastName'               => $claroPartnerCpf,
            'cpf'                    => $claroPartnerCpf,
            'email'                  => 'vendedor_aa@teste.com',
            "areaCode"               => "11",
            "activationStatusCode"   => UserStatus::ACTIVE,
            "integrationCredentials" => "{}",
            "password"               => $this->password
        ]);
        $claroPartnerUser->role()->associate($this->salesman)->save();
        if (!$claroPartnerUser->pointsOfSale()->first()) {
            $claroPartnerUser->pointsOfSale()->attach($this->pointOfSale);
        }
    }
}
