<?php

namespace ClaroBR\Tests\Helpers;

use ClaroBR\Models\ClaroBandaLarga;
use ClaroBR\Models\ClaroPos;
use ClaroBR\Models\ClaroPre;
use ClaroBR\Models\ControleBoleto;
use ClaroBR\Models\ControleFacil;
use Illuminate\Database\Eloquent\Factory;

class ClaroServices
{
    public static function ClaroBandaBarga() :ClaroBandaLarga
    {
        return self::loadFactory()
            ->of(ClaroBandaLarga::class)
            ->make();
    }

    private static function loadFactory(): Factory
    {
        $factory = Factory::construct(
            \Faker\Factory::create(),
            base_path('modules/clarobr/Factories/')
        );
        return $factory;
    }

    public static function ClaroPos() :ClaroPos
    {
        return self::loadFactory()
            ->of(ClaroPos::class)
            ->make();
    }

    public static function ClaroPre() :ClaroPre
    {
        return self::loadFactory()
            ->of(ClaroPre::class)
            ->make();
    }

    public static function ControleBoleto() :ControleBoleto
    {
        return self::loadFactory()
            ->of(ControleBoleto::class)
            ->make();
    }

    public static function ControleFacil() :ControleFacil
    {
        return self::loadFactory()
            ->of(ControleFacil::class)
            ->make();
    }
}
