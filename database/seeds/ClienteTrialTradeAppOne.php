<?php

use Illuminate\Database\Seeder;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\Permissions\DashboardPermission;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Domain\Enumerators\UserStatus;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\Service;
use TradeAppOne\Domain\Models\Tables\User;

/**
 * @@property  Role salesman
 */
class ClienteTrialTradeAppOne extends Seeder
{
    private $password;
    private $network;
    private $pointOfSale;
    private $salesman;

    public function run(): void
    {
        $this->password = bcrypt('Trial@2019');
        $this->createClaroPartnerNetwork();
    }

    private function createClaroPartnerNetwork(): void
    {
        $this->network = Network::firstOrNew(['cnpj' => '10002002000014']);
        $services      = Service::all();

        $this->network->fill([
            'slug'              => 'rede-trial',
            'label'             => 'Rede Trial',
            'cnpj'              => '10002002000014',
            'tradingName'       => 'Rede Trial',
            'companyName'       => 'Rede Trial',
            'preferences'       => json_encode([
                'initialPage' => 'painel/activation'
            ]),
        ])->save();

        $this->network->services()->sync($services);

        $this->createClaroPointOfSale();
    }

    public function createClaroPointOfSale(): void
    {
        $this->pointOfSale = PointOfSale::firstOrNew(['cnpj' => '55478991320004']);
        $this->pointOfSale->fill([
            'label'                  => 'Rede Trial',
            'slug'                   => 'rede-trial',
            'tradingName'            => 'Rede Trial',
            'companyName'            => 'Rede Trial',
            'cnpj'                   => '55478991320004',
            'areaCode'               => '11',
            'availableServices'      => '{}',
            'zipCode'                => '',
            'local'                  => '',
            'neighborhood'           => '',
            'state'                  => 'SP',
            'number'                 => 0,
            'city'                   => 'Sao Paulo',
            'complement'             => ''
        ]);
        $this->pointOfSale->network()->associate($this->network)->save();
        $this->createClaroRole();
    }

    public function createClaroRole()
    {
        $this->salesman = Role::firstOrNew([
            'name'  => 'Homologador Claro',
            'slug'  => 'homologador-claro',
            'level' => '1000'
        ]);

        $permissions = [
            SalePermission::NAME.SalePermission::CREATE,
            DashboardPermission::NAME.DashboardPermission::VIEW
        ];

        $this->salesman->network()->associate($this->network)->save();

        foreach ($permissions as $permission) {
            $instance = Permission::where('slug', $permission)->first();
            $this->salesman->stringPermissions()->attach($instance);
        }

        $this->salesman->save();
        $this->createClaroPartnerUser();
    }

    private function createClaroPartnerUser(): void
    {
        $claroPartnerCpf  = '88652645000';
        $claroPartnerUser = User::firstOrNew(['cpf' => $claroPartnerCpf]);

        $claroPartnerUser->fill([
            'firstName'              => 'Usuario Trial',
            'lastName'               => $claroPartnerCpf,
            'cpf'                    => $claroPartnerCpf,
            'email'                  => 'vendedor_aa@teste.com',
            'areaCode'               => '11',
            'activationStatusCode'   => UserStatus::ACTIVE,
            'integrationCredentials' => '{}',
            'password'               => $this->password
        ]);
        $claroPartnerUser->role()->associate($this->salesman)->save();
        if (!$claroPartnerUser->pointsOfSale()->first()) {
            $claroPartnerUser->pointsOfSale()->attach($this->pointOfSale);
        }
    }
}
