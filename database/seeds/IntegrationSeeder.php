<?php

use Illuminate\Database\Seeder;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Enumerators\Permissions\DashboardPermission;
use TradeAppOne\Domain\Enumerators\Permissions\ManagementReportPermission;
use TradeAppOne\Domain\Enumerators\Permissions\ManualPermission;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Enumerators\UserStatus;
use TradeAppOne\Domain\Models\Tables\Hierarchy;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\User;

class IntegrationSeeder extends Seeder
{
    private $password;

    public function run()
    {
        $this->password = bcrypt('Trade@2019');
        $this->execute();
    }

    private function execute(): void
    {
        $tradeHierarchy = Hierarchy::firstOrNew(['slug' => 'trade-up-group']);
        $tradeHierarchy->fill([
            'label' => 'Trade Up Group',
            'slug' => 'trade-up-group',
            'parent' => null,
            'networkId' => null,
        ])->save();

        $tradeupNetwork = Network::firstOrNew(['cnpj' => '22696923000162']);
        $tradeupNetwork->fill([
            "slug"              => "tradeup-group",
            "label"             => "TradeUp Group",
            "cnpj"              => "22696923000162",
            "tradingName"       => "Trade Up LTDA",
            "companyName"       => "TradeUp Group",
            "availableServices" => $this->fetchAllServices()

        ])->save();

        $iplaceNetwork = Network::firstOrNew(['cnpj' => '00000000000000']);
        $iplaceNetwork->fill([
            "slug"              => "iplace",
            "label"             => "Iplace",
            "cnpj"              => "00000000000000",
            "tradingName"       => "Iplace",
            "companyName"       => "Iplace",
            "availableServices" => $this->fetchAllServices(),

        ])->save();

        $network = Network::firstOrNew(['cnpj' => '00000000000002']);
        $network->fill([
            "slug"              => "riachuelo",
            "label"             => "Riachuelo",
            "cnpj"              => "00000000000002",
            "tradingName"       => "Riachuelo",
            "companyName"       => "Riachuelo",
            "availableServices" => $this->fetchAllServices(),
            

        ])->save();

        $riachueloHierarchy = Hierarchy::firstOrNew(['slug' => NetworkEnum::RIACHUELO]);
        $riachueloHierarchy->fill([
            'label' => 'riachuelo',
            'parent' => $tradeHierarchy->id,
            'networkId' => $network->id
        ]);

        $riachueloHierarchy->save();

        $ceaNetwork = Network::firstOrNew(['cnpj' => '45242914000105']);
        $ceaNetwork->fill([
            "slug"              => "cea",
            "label"             => "C&A",
            "cnpj"              => "45242914000105",
            "tradingName"       => "Cea",
            "companyName"       => "CEA MODAS LTDA",
            "availableServices" => $this->fetchAllServices(),
        ])->save();


        $pointOfSaleXpto = PointOfSale::firstOrNew(['cnpj' => '33200056030803']);
        $pointOfSaleXpto->fill([
            "label"                  => "Loja Teste",
            "slug"                   => "loja-xpto",
            "tradingName"            => "Rede Teste",
            "companyName"            => "",
            "cnpj"                   => "33200056030803",
            "areaCode"               => "11",
            "providerIdentifiers"    => json_encode([
                "claroBR" => "15F0",
                "OI"      => "1010892"
            ]),
            "zipCode"                => "90520-003",
            "local"                  => "AV PLINIO BRASIL MILANO",
            "neighborhood"           => "PASSO DAREIA",
            "state"                  => "RS",
            "number"                 => 2333.0,
            "city"                   => "PORTO ALEGRE",
            "complement"             => ""
        ]);
        $pointOfSaleXpto->network()->associate($network)->save();

        $admin = Role::query()->firstOrCreate([
            'slug' => 'administrator',
            'name' =>'Administrator',
            'networkId' => $tradeupNetwork->id
        ]);

        $gerenteLoja = Role::query()->firstOrCreate([
            'slug' => 'gerente-de-rede-test',
            'name' =>'gerente-de-rede-test',
            'networkId' => $network->id,
            'parent' => $admin->id
        ]);

        $salesman  = Role::query()->firstOrNew([
            'slug' => 'vendedor-teste',
            'name' => 'vendedor-teste',
            'networkId' => $network->id,
            'parent' => $gerenteLoja->id
        ]);

        $salesman->save();

        $vivoPointOfSale = PointOfSale::firstOrNew(['cnpj' => '89237911008559']);
        $vivoPointOfSale->fill([
            "label"                  => "Iplace - 603",
            "slug"                   => "603",
            "tradingName"            => "IPLACE MOBILE",
            "companyName"            => "",
            "cnpj"                   => "89237911008559",
            "areaCode"               => "11",
            "providerIdentifiers"    => json_encode([
                "claroBR" => "15F0",
            ]),
            "zipCode"                => "90520-003",
            "local"                  => "AV PLINIO BRASIL MILANO",
            "neighborhood"           => "PASSO DAREIA",
            "state"                  => "RS",
            "number"                 => 2333.0,
            "city"                   => "PORTO ALEGRE",
            "complement"             => ""
        ]);
        $vivoPointOfSale->network()->associate($iplaceNetwork)->save();

        $nextelPointOfSale = PointOfSale::firstOrNew(['cnpj' => '45242914022680']);
        $nextelPointOfSale->fill([
            "label"                  => "GBS",
            "slug"                   => "GBS",
            "tradingName"            => "C&A MODAS",
            "companyName"            => "BONSUCESSO SHOPPING",
            "cnpj"                   => "45242914022680",
            "areaCode"               => "11",
            "providerIdentifiers"    => json_encode([
                "claroBR" => "15F0",
                "OI"      => "1018574",
                "TIM"     => "SP10_MGABCI_VA0008_A007",
                "NEXTEL"  => [
                    "cod" => "787489",
                    "ref" => "54154"
                ]

            ]),
        ]);
        $nextelPointOfSale->network()->associate($ceaNetwork)->save();

        $this->vivoUser($salesman, $pointOfSaleXpto);
        $this->vivoUser2($salesman, $vivoPointOfSale);
        $this->claroUser1($salesman, $pointOfSaleXpto);
        $this->claroUser2($salesman, $pointOfSaleXpto);
        $this->timUser($salesman, $nextelPointOfSale);
        $this->oiUser1($salesman, $pointOfSaleXpto);
        $this->oiUser2($salesman, $pointOfSaleXpto);
        $this->nextelUser($salesman, $nextelPointOfSale);
    }

    /**
     * @param $sivPasswordTest
     * @return User
     */
    private function claroUser1($role, $pointOfSale)
    {
        $cpf       = '01002957036';
        $claroUser = User::firstOrNew(['cpf' => $cpf]);
        $claroUser->fill([
            'firstName'              => 'Vendedor Claro B1',
            'lastName' => $cpf,
            'email'                  => 'claro@tradeupgroup.com',
            'cpf' => $cpf,
            "areaCode"               => "11",
            "activationStatusCode"   => UserStatus::ACTIVE,
            "integrationCredentials" => json_encode([
                "siv" => [
                    "id"  => "18",
                    "cpf" => "01296802140",
                ]
            ]),
            "password"               => $this->password,
        ]);

        $claroUser->role()->associate($role)->save();
        if (! $claroUser->pointsOfSale()->first()) {
            $claroUser->pointsOfSale()->attach($pointOfSale);
        }
    }

    private function claroUser2($role, $pointOfSale)
    {
        $claroUser = User::firstOrNew(['cpf' => '04734324123']);
        $claroUser->fill([
            'firstName'              => 'Vendedor Claro B2',
            'lastName'               => '04734324123',
            'email'                  => 'claro@tradeupgroup.com',
            'cpf'                    => '04734324123',
            "areaCode"               => "11",
            "activationStatusCode"   => UserStatus::ACTIVE,
            "password"               => $this->password,
        ]);

        $claroUser->role()->associate($role)->save();
        if (! $claroUser->pointsOfSale()->first()) {
            $claroUser->pointsOfSale()->attach($pointOfSale);
        }
    }

    /**
     * @param $admin
     * @param $pointOfSaleXpto
     */
    private function vivoUser($admin, $pointOfSaleXpto): void
    {
        $vivoUser = User::firstOrNew(['cpf' => '44231267880']);
        $vivoUser->fill([
            'firstName'              => 'Vendedor Vivo B1',
            'lastName'               => '44231267880',
            'cpf'                    => '44231267880',
            'email'                  => 'vivo@vivo.com.br',
            "areaCode"               => "11",
            "activationStatusCode"   => UserStatus::ACTIVE,
            "password"               => $this->password,
        ]);
        $vivoUser->role()->associate($admin)->save();
        if (! $vivoUser->pointsOfSale()->first()) {
            $vivoUser->pointsOfSale()->attach($pointOfSaleXpto);
        }
    }

    private function vivoUser2($admin, $pointOfSaleXpto): void
    {
        $vivoUser = User::firstOrNew(['cpf' => '02722024012']);
        $vivoUser->fill([
            'firstName'              => 'Vendedor Vivo B2',
            'lastName'               => '02722024012',
            'cpf'                    => '02722024012',
            'email'                  => 'vivo@vivo.com.br',
            "areaCode"               => "11",
            "activationStatusCode"   => UserStatus::ACTIVE,
            "password"               => $this->password,
        ]);
        $vivoUser->role()->associate($admin)->save();
        if (! $vivoUser->pointsOfSale()->first()) {
            $vivoUser->pointsOfSale()->attach($pointOfSaleXpto);
        }
    }

    private function timUser($admin, $pointOfSaleXpto): void
    {
        $timUser = \TimBR\Tests\TimBRTestBook::SUCCESS_USER;

        $vivoUser = User::firstOrNew(['cpf' => $timUser]);
        $vivoUser->fill([
            'firstName'              => 'Vendedor Tim B1',
            'lastName'               => \TimBR\Tests\TimBRTestBook::SUCCESS_USER,
            'cpf'                    => $timUser,
            'email'                  => 'tim@tim.com.br',
            "areaCode"               => "11",
            "activationStatusCode"   => UserStatus::ACTIVE,
            "password"               => $this->password,
        ]);
        $vivoUser->role()->associate($admin)->save();
        if (! $vivoUser->pointsOfSale()->first()) {
            $vivoUser->pointsOfSale()->attach($pointOfSaleXpto);
        }
    }

    private function oiUser1($admin, $pointOfSaleXpto): void
    {
        $oiUser = '46003079843';

        $oiUser = User::firstOrNew(['cpf' => $oiUser]);
        $oiUser->fill([
            'firstName'              => 'Vendedor Oi B1',
            'lastName'               => '46003079843',
            'cpf'                    => '46003079843',
            'email'                  => 'oi@oi.com.br',
            "areaCode"               => "11",
            "activationStatusCode"   => UserStatus::ACTIVE,
            "password"               => $this->password,
        ]);
        $oiUser->role()->associate($admin)->save();
        if (! $oiUser->pointsOfSale()->first()) {
            $oiUser->pointsOfSale()->attach($pointOfSaleXpto);
        }
    }

    private function oiUser2($admin, $pointOfSaleXpto): void
    {
        $oiUser = '46005092855';

        $oiUser = User::firstOrNew(['cpf' => $oiUser]);
        $oiUser->fill([
            'firstName'              => 'Vendedor Oi B2',
            'lastName'               => '46005092855',
            'cpf'                    => '46005092855',
            'email'                  => 'oi@oi.com.br',
            "areaCode"               => "11",
            "activationStatusCode"   => UserStatus::ACTIVE,
            "password"               => $this->password,
        ]);
        $oiUser->role()->associate($admin)->save();
        if (! $oiUser->pointsOfSale()->first()) {
            $oiUser->pointsOfSale()->attach($pointOfSaleXpto);
        }
    }

    private function nextelUser($admin, $pointOfSaleXpto): void
    {
        $nextelUser = '44364323861';

        $oiUser = User::firstOrNew(['cpf' => $nextelUser]);
        $oiUser->fill([
            'firstName'              => 'Vendedor Nextel B2',
            'lastName'               => $nextelUser,
            'cpf'                    => $nextelUser,
            'email'                  => 'nextel@nextel.com.br',
            "areaCode"               => "11",
            "activationStatusCode"   => UserStatus::ACTIVE,
            "password"               => $this->password,
        ]);
        $oiUser->role()->associate($admin)->save();
        if (! $oiUser->pointsOfSale()->first()) {
            $oiUser->pointsOfSale()->attach($pointOfSaleXpto);
        }
    }

    public function fetchAllServices()
    {
        return json_encode([
            Operations::LINE_ACTIVATION => [
                Operations::OI     => [
                    Operations::OI_CONTROLE_BOLETO,
                    Operations::OI_CONTROLE_CARTAO
                ],
                Operations::TIM    => [
                    Operations::TIM_EXPRESS,
                    Operations::TIM_CONTROLE_FATURA
                ],
                Operations::VIVO   => [
                    Operations::VIVO_CONTROLE_CARTAO,
                    Operations::VIVO_CONTROLE,
                    Operations::VIVO_POS_PAGO,
                    Operations::VIVO_PRE
                ],
                Operations::CLARO  => [
                    Operations::CLARO_CONTROLE_BOLETO,
                    Operations::CLARO_CONTROLE_FACIL,
                    Operations::CLARO_POS,
                    Operations::CLARO_PRE,
                    Operations::CLARO_BANDA_LARGA
                ],
                Operations::NEXTEL => [
                    Operations::NEXTEL_CONTROLE_BOLETO,
                    Operations::NEXTEL_CONTROLE_CARTAO
                ]
            ],
            Operations::MOBILE_APPS     => [
                Operations::MOVILE => [
                    Operations::MOVILE_CUBES
                ]
            ],
            Operations::SECURITY        => [
                Operations::MCAFEE => [
                    Operations::MCAFEE_MOBILE_SECURITY
                ]
            ],
            Operations::TRADE_IN        => [
                Operations::TRADE_IN_MOBILE => [
                    Operations::SALDAO_INFORMATICA
                ]
            ]
        ]);
    }
}
