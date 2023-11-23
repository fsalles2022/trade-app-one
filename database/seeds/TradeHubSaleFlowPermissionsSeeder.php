<?php

declare(strict_types=1);

use Illuminate\Database\Seeder;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Models\Tables\Permission;

class TradeHubSaleFlowPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        Permission::updateOrCreate(
            [
                'slug' => self::getPrePagoSlug(),
            ],
            [
                'label'  => 'Fluxo de vendas Trade HUB para Pré Pago',
                'slug'   => self::getPrePagoSlug(),
                'client' => SubSystemEnum::WEB,
            ]
        );

        Permission::updateOrCreate(
            [
                'slug' => self::getControleFacilSlug(),
            ],
            [
                'label'  => 'Fluxo de vendas Trade HUB para Controle Fácil',
                'slug'   => self::getControleFacilSlug(),
                'client' => SubSystemEnum::WEB,
            ]
        );

        Permission::updateOrCreate(
            [
                'slug' => self::getControleBoletoSlug(),
            ],
            [
                'label'  => 'Fluxo de vendas Trade HUB para Controle Boleto',
                'slug'   => self::getControleBoletoSlug(),
                'client' => SubSystemEnum::WEB,
            ]
        );

        Permission::updateOrCreate(
            [
                'slug' => self::getResidentialSlug(),
            ],
            [
                'label'  => 'Fluxo de vendas Trade HUB para Residencial',
                'slug'   => self::getResidentialSlug(),
                'client' => SubSystemEnum::WEB,
            ]
        );

        Permission::updateOrCreate(
            [
                'slug' => self::getTradeHubRcvSlug(),
            ],
            [
                'label'  => 'Fluxo de Back Office Rcv Trade HUB',
                'slug'   => self::getTradeHubRcvSlug(),
                'client' => SubSystemEnum::WEB,
            ]
        );

        Permission::updateOrCreate(
            [
                'slug' => self::getTradeHubSaleManagerSlug(),
            ],
            [
                'label'  => 'Fluxo de Back Office Gerenciamento de venda Trade HUB',
                'slug'   => self::getTradeHubSaleManagerSlug(),
                'client' => SubSystemEnum::WEB,
            ]
        );

        Permission::updateOrCreate(
            [
                'slug' => self::getTradeHubSaleList(),
            ],
            [
                'label'  => 'Fluxo de Listagens de vendas',
                'slug'   => self::getTradeHubSaleList(),
                'client' => SubSystemEnum::WEB,
            ]
        );

        Permission::updateOrCreate(
            [
                'slug' => self::getTradeHubSaleAdministratorSlug(),
            ],
            [
                'label'  => 'Fluxo de permissão de administrador para listagens de vendas',
                'slug'   => self::getTradeHubSaleAdministratorSlug(),
                'client' => SubSystemEnum::WEB,
            ]
        );

        Permission::updateOrCreate(
            [
                'slug' => self::getTimPrePagoSlug(),
            ],
            [
                'label'  => 'Fluxo de vendas Trade HUB para TIM Pré Pago',
                'slug'   => self::getTimPrePagoSlug(),
                'client' => SubSystemEnum::WEB,
            ]
        );

        Permission::updateOrCreate(
            [
                'slug' => self::getTimControleExpressSlug(),
            ],
            [
                'label'  => 'Fluxo de vendas Trade HUB para TIM Controle Express',
                'slug'   => self::getTimControleExpressSlug(),
                'client' => SubSystemEnum::WEB,
            ]
        );
    }

    public static function getPrePagoSlug(): string
    {
        return SalePermission::NAME . '.' . SalePermission::TRADE_HUB_CLARO_PRE_PAGO;
    }

    public static function getControleFacilSlug(): string
    {
        return SalePermission::NAME . '.' . SalePermission::TRADE_HUB_CLARO_CONTROLE_FACIL;
    }

    public static function getControleBoletoSlug(): string
    {
        return SalePermission::NAME . '.' . SalePermission::TRADE_HUB_CLARO_CONTROLE_BOLETO;
    }

    public static function getResidentialSlug(): string
    {
        return SalePermission::NAME . '.' . SalePermission::TRADE_HUB_CLARO_RESIDENTIAL;
    }

    public static function getTradeHubRcvSlug(): string
    {
        return SalePermission::NAME . '.' . SalePermission::TRADE_HUB_BACK_OFFICE_RCV;
    }

    public static function getTradeHubSaleManagerSlug(): string
    {
        return SalePermission::NAME . '.' . SalePermission::TRADE_HUB_BACK_OFFICE_SALE_MANAGER;
    }

    public static function getTradeHubSaleList(): string
    {
        return SalePermission::NAME . '.' . SalePermission::TRADE_HUB_SALE_LIST;
    }

    public static function getTradeHubSaleAdministratorSlug(): string
    {
        return SalePermission::NAME . '.' . SalePermission::TRADE_HUB_SALE_ADMINISTRATOR;
    }

    public static function getTimPrePagoSlug(): string
    {
        return SalePermission::NAME . '.' . SalePermission::TRADE_HUB_TIM_PRE_PAGO;
    }

    public static function getTimControleExpressSlug(): string
    {
        return SalePermission::NAME . '.' . SalePermission::TRADE_HUB_TIM_CONTROLE_EXPRESS;
    }
}
