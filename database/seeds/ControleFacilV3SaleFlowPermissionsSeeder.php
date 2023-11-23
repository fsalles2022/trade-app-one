<?php

declare(strict_types=1);

use Illuminate\Database\Seeder;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Models\Tables\Permission;

class ControleFacilV3SaleFlowPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        Permission::updateOrCreate(
            [
                'slug' => self::getControleFacilV3Slug(),
            ],
            [
                'label'  => 'Fluxo de vendas 3.0 para Controle Fácil (Back TradeHUB)',
                'slug'   => self::getControleFacilV3Slug(),
                'client' => SubSystemEnum::WEB,
            ]
        );

        Permission::updateOrCreate(
            [
                'slug' => self::getControleFacilV3ActivationSlug(),
            ],
            [
                'label'  => 'Fluxo de vendas 3.0 para Controle Fácil (Back TradeHUB) permitir fazer ativação',
                'slug'   => self::getControleFacilV3ActivationSlug(),
                'client' => SubSystemEnum::WEB,
            ]
        );

        Permission::updateOrCreate(
            [
                'slug' => self::getControleFacilV3MigrationSlug(),
            ],
            [
                'label'  => 'Fluxo de vendas 3.0 para Controle Fácil (Back TradeHUB) permitir fazer migração',
                'slug'   => self::getControleFacilV3MigrationSlug(),
                'client' => SubSystemEnum::WEB,
            ]
        );

        Permission::updateOrCreate(
            [
                'slug' => self::getControleFacilV3PortabilitySlug(),
            ],
            [
                'label'  => 'Fluxo de vendas 3.0 para Controle Fácil (Back TradeHUB) permitir fazer portabilidade',
                'slug'   => self::getControleFacilV3PortabilitySlug(),
                'client' => SubSystemEnum::WEB,
            ]
        );
    }

    public static function getControleFacilV3Slug(): string
    {
        return SalePermission::NAME . '.' . SalePermission::CONTROLE_FACIL_V3;
    }

    public static function getControleFacilV3ActivationSlug(): string
    {
        return SalePermission::NAME . '.' . SalePermission::CONTROLE_FACIL_V3_ACTIVATION;
    }

    public static function getControleFacilV3MigrationSlug(): string
    {
        return SalePermission::NAME . '.' . SalePermission::CONTROLE_FACIL_V3_MIGRATION;
    }

    public static function getControleFacilV3PortabilitySlug(): string
    {
        return SalePermission::NAME . '.' . SalePermission::CONTROLE_FACIL_V3_PORTABILITY;
    }
}
