<?php

namespace TradeAppOne\Domain\Enumerators;

use Outsourced\Cea\Importable\CeaGiftCardImportable;
use Recommendation\Importables\RecommendationImportable;
use Reports\Goals\Importables\GoalImportable;
use TradeAppOne\Domain\Importables\DevicesNetworkImportable;
use TradeAppOne\Domain\Importables\EvaluationBonusImportable;
use TradeAppOne\Domain\Importables\EvaluationImportable;
use TradeAppOne\Domain\Importables\OiResidentialSaleImportable;
use TradeAppOne\Domain\Importables\AutomaticRegistrationImportable;
use TradeAppOne\Domain\Importables\PointOfSaleImportable;
use TradeAppOne\Domain\Importables\ServicesImportable;
use TradeAppOne\Domain\Importables\TimBRRebateImportable;
use TradeAppOne\Domain\Importables\UserImportable;
use TradeAppOne\Domain\Importables\UserImportableDelete;
use TradeAppOne\Domain\Importables\PasswordMassUpdateImportable;

final class Importables
{
    public const USERS                  = 'USERS';
    public const USERS_DELETE           = 'USERS_DELETE';
    public const POINTS_OF_SALE         = 'POINTS_OF_SALE';
    public const EVALUATIONS            = 'EVALUATIONS';
    public const EVALUATIONS_BONUS      = 'EVALUATIONS_BONUS';
    public const DEVICES_NETWORK        = 'DEVICES_NETWORK';
    public const GOALS                  = 'GOALS';
    public const DEVICES_OUTSOURCED     = 'DEVICES_OUTSOURCED';
    public const CEA_GIFT_CARDS         = 'CEA_GIFT_CARDS';
    public const RECOMMENDATIONS        = 'RECOMMENDATIONS';
    public const SERVICES               = 'SERVICES';
    public const OI_RESIDENTIAL_SALE    = 'OI_RESIDENTIAL_SALE';
    public const AUTOMATIC_REGISTRATION = 'AUTOMATIC_REGISTRATION';
    public const PASSWORD_MASS_UPDATE   = 'PASSWORD_MASS_UPDATE';
    public const TIM_REBATE             = 'TIM_REBATE';

    public const IMPORTABLES = [
        self::USERS                     => UserImportable::class,
        self::USERS_DELETE              => UserImportableDelete::class,
        self::POINTS_OF_SALE            => PointOfSaleImportable::class,
        self::EVALUATIONS               => EvaluationImportable::class,
        self::EVALUATIONS_BONUS         => EvaluationBonusImportable::class,
        self::DEVICES_NETWORK           => DevicesNetworkImportable::class,
        self::GOALS                     => GoalImportable::class,
        self::CEA_GIFT_CARDS            => CeaGiftCardImportable::class,
        self::RECOMMENDATIONS           => RecommendationImportable::class,
        self::SERVICES                  => ServicesImportable::class,
        self::OI_RESIDENTIAL_SALE       => OiResidentialSaleImportable::class,
        self::AUTOMATIC_REGISTRATION    => AutomaticRegistrationImportable::class,
        self::PASSWORD_MASS_UPDATE      => PasswordMassUpdateImportable::class,
        self::TIM_REBATE                => TimBRRebateImportable::class,
    ];
}
