<?php

use Illuminate\Database\Seeder;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Enumerators\Permissions\DashboardPermission;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Enumerators\UserStatus;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\User;

class BuybackPartnerManagementSeeder extends Seeder
{
    private $password;
    private $network;
    private $pointOfSale;
    private $salesman;

    const SALDAO_NETWORK_CNPJ       = '10002002000003';
    const SALDAO_POINT_OF_SALE_CNPJ = '55478991320002';

    const RONI_GERWER = [
        'NAME' => 'Roni Gerwer',
        'CPF' => '23542008893',
    ];

    const MARCOS_SANTOS = [
        'NAME' => 'Marcos Santos',
        'CPF' => '08976369882',
    ];

    public function run()
    {
        $this->password = bcrypt('saldao@2019');
        $this->createSaldaoPartnerNetwork();
    }

    private function createSaldaoPartnerNetwork()
    {
        $this->network = Network::firstOrNew(['cnpj' => self::SALDAO_NETWORK_CNPJ]);
        $this->network->fill([
            "slug" => "Parceiro Saldao",
            "label" => "Parceiro Saldao",
            "cnpj" => self::SALDAO_NETWORK_CNPJ,
            "tradingName" => "Parceiro Saldao",
            "companyName" => "Parceiro Saldao",
            "availableServices" => json_encode([
                Operations::TRADE_IN => [
                    Operations::TRADE_IN_MOBILE => [
                        Operations::SALDAO_INFORMATICA
                    ]
                ]
            ]),
            "preferences" => json_encode([
                "initialPage" => "painel/activation"
            ]),
        ])->save();
        $this->createSaldaoPointOfSale();
    }

    public function createSaldaoPointOfSale()
    {
        $this->pointOfSale = PointOfSale::firstOrNew(['cnpj'=> self::SALDAO_POINT_OF_SALE_CNPJ]);
        $this->pointOfSale->fill([
            "label"                  => "Loja Saldao",
            "slug"                   => "loja-saldao",
            "tradingName"            => "Loja Saldao",
            "companyName"            => "",
            "cnpj"                   => self::SALDAO_POINT_OF_SALE_CNPJ,
            "areaCode"               => "11",
            "zipCode"                => "",
            "local"                  => "",
            "neighborhood"           => "",
            "state"                  => "SP",
            "number"                 => 0,
            "city"                   => "",
            "complement"             => ""
        ]);
        $this->pointOfSale->network()->associate($this->network)->save();
        $this->createSaldaoRole();
    }

    public function createSaldaoRole()
    {
        $this->salesman = Role::firstOrNew(['slug' => 'vendedor-saldao']);
        $this->salesman->fill([
            "name" => "Vendedor-saldao",
            "slug" => "vendedor-saldao",
            "level" => "1000",
            "permissions" => json_encode([
                SubSystemEnum::API => [
                    SalePermission::NAME => [
                        PermissionActions::VIEW_ONLY_TRADE_IN,
                        PermissionActions::CREATE
                    ],
                ],
                SubSystemEnum::WEB => [
                    DashboardPermission::NAME => [PermissionActions::VIEW],
                    SalePermission::NAME      => [
                        PermissionActions::VIEW_ONLY_TRADE_IN, PermissionActions::CREATE
                    ],
                ]
            ])
        ]);
        $this->salesman->network()->associate($this->network)->save();
        $this->createSaldaoPartnerSalesman();
        $this->createSaldaoPartnerManager();
    }

    private function createSaldaoPartnerSalesman(): void
    {
        $saldaoPartnerUser = User::firstOrNew(['cpf' => self::MARCOS_SANTOS['CPF']]);
        $saldaoPartnerUser->fill([
            'firstName' => self::MARCOS_SANTOS['NAME'],
            'lastName' => self::MARCOS_SANTOS['CPF'],
            'cpf' => self::MARCOS_SANTOS['CPF'],
            'email' => 'marcos.santos@cnservices.com.br',
            "areaCode" => "11",
            "activationStatusCode" => UserStatus::ACTIVE,
            "integrationCredentials" => "{}",
            "password" => $this->password
        ]);
        $saldaoPartnerUser->role()->associate($this->salesman)->save();
        if (! $saldaoPartnerUser->pointsOfSale()->first()) {
            $saldaoPartnerUser->pointsOfSale()->attach($this->pointOfSale);
        }
    }

    private function createSaldaoPartnerManager(): void
    {
        $saldaoPartnerUser = User::firstOrNew(['cpf' => self::RONI_GERWER['CPF']]);
        $saldaoPartnerUser->fill([
            'firstName' => self::RONI_GERWER['NAME'],
            'lastName' => self::RONI_GERWER['CPF'],
            'cpf' => self::RONI_GERWER['CPF'],
            'email' => 'roni@saldaodainformatica.com.br',
            "areaCode" => "11",
            "activationStatusCode" => UserStatus::ACTIVE,
            "integrationCredentials" => "{}",
            "password" => $this->password
        ]);
        $saldaoPartnerUser->role()->associate($this->salesman)->save();
        if (! $saldaoPartnerUser->pointsOfSale()->first()) {
            $saldaoPartnerUser->pointsOfSale()->attach($this->pointOfSale);
        }
    }
}
