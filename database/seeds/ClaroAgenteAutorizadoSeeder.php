<?php

use Illuminate\Database\Seeder;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\UserStatus;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\Service;
use TradeAppOne\Domain\Models\Tables\User;

class ClaroAgenteAutorizadoSeeder extends Seeder
{
    private $password;
    private $network;
    private $pointOfSale;
    private $salesman;

    public function run()
    {
        $this->password = bcrypt('Cl@ro2019');
        $this->createClaroPartnerNetwork();
    }

    private function createClaroPartnerNetwork(): void
    {
        $this->network = Network::firstOrNew(['cnpj' => '10002002000002']);
        $services      = Service::query()->where('operator', Operations::CLARO)->get();
        $this->network->fill([
            'slug'              => 'parceiro-claro',
            'label'             => 'Parceiro Claro',
            'cnpj'              => '10002002000002',
            'tradingName'       => 'Parceiro Claro',
            'companyName'       => 'Parceiro Claro',
            'preferences'       => json_encode([
                'initialPage' => 'painel/activation'
            ]),
        ])->save();

        $this->network->services()->sync($services);

        $this->createClaroPointOfSale();
    }

    public function createClaroPointOfSale(): void
    {
        $this->pointOfSale = PointOfSale::firstOrNew(['cnpj' => '55478991320001']);
        $this->pointOfSale->fill([
            'label'                  => 'Loja Teste',
            'slug'                   => 'loja-teste',
            'tradingName'            => 'Rede Teste',
            'companyName'            => '',
            'cnpj'                   => '55478991320001',
            'areaCode'               => '11',
            'providerIdentifiers'    => json_encode([
                'claroBR' => '15F0'
            ]),
            'availableServices'      => '{}',
            'zipCode'                => '',
            'local'                  => '',
            'neighborhood'           => '',
            'state'                  => 'SP',
            'number'                 => 0,
            'city'                   => '',
            'complement'             => ''
        ]);
        $this->pointOfSale->network()->associate($this->network)->save();
        $this->createClaroRole();
    }

    public function createClaroRole(): void
    {
        $admin = Role::query()->where([
            'slug' => 'administrator',
            'name' => 'Administrator',
        ])->first();

        $this->salesman = Role::firstOrNew(['slug' => 'vendedor-claro']);
        $this->salesman->fill([
            'name'        => 'Vendedor-claro',
            'slug'        => 'vendedor-claro',
            'parent'      => $admin->id
        ]);
        $this->salesman->network()->associate($this->network)->save();
        $this->createClaroPartnerUser();
    }

    private function createClaroPartnerUser(): void
    {
        $claroPartnerCpf = '86663714040';

        $claroPartnerUser = User::firstOrNew(['cpf' => $claroPartnerCpf]);
        $claroPartnerUser->fill([
            'firstName'              => 'VENDEDOR AA',
            'lastName'               => $claroPartnerCpf,
            'cpf'                    => $claroPartnerCpf,
            'email'                  => 'vendedor_aa@teste.com',
            'areaCode'               => '11',
            'activationStatusCode'   => UserStatus::ACTIVE,
            'integrationCredentials' => '{}',
            'password'               => $this->password
        ]);
        $claroPartnerUser->role()->associate($this->salesman)->save();
        if (! $claroPartnerUser->pointsOfSale()->first()) {
            $claroPartnerUser->pointsOfSale()->attach($this->pointOfSale);
        }
    }
}
