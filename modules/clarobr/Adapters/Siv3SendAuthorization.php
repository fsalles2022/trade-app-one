<?php

declare(strict_types=1);

namespace ClaroBR\Adapters;

use ClaroBR\Exceptions\Siv3Exceptions;
use TradeAppOne\Domain\Enumerators\Operations;

class Siv3SendAuthorization extends Siv3PayloadAdapter
{
    /** @var string|null */
    private $phoneNumber;

    /** @var string|null */
    private $origin;

    /** @var string|null */
    private $notificationTypes;

    public const CONTROLE_BOLETO_TYPE = 'tradeappone-controle-boleto';
    public const PRE_PAGO_TYPE        = 'tradeappone-pre-pago';
    public const POS_PAGO_TYPE        = 'tradeappone-pos-pago';
    public const CONTROLE_FACIL_TYPE  = 'tradeappone-controle-facil';

    public function __construct(?string $phoneNumber, ?string $origin, ?string $notificationTypes)
    {
        $this->phoneNumber       = $phoneNumber;
        $this->origin            = $origin;
        $this->notificationTypes = $notificationTypes;
    }

    public function getPhoneNumber(): ?string
    {
        return str_contains($this->phoneNumber, '+55') === false ? '+55' . $this->phoneNumber : $this->phoneNumber;
    }

    public function getOrigin(): string
    {
        $origin = $this->getOriginTypes()[$this->origin] ?? null;

        throw_if($origin === null, Siv3Exceptions::unauthorizedOperation());

        return $origin;
    }

    public function getType(): ?string
    {
        return $this->notificationTypes;
    }

    /** @return mixed[] */
    public function jsonSerialize(): array
    {
        return [
            'type' => $this->getType(),
            'phoneNumber' => $this->getPhoneNumber(),
            'origin' => $this->getOrigin()
        ];
    }

    /** @return string[] */
    public function getOriginTypes(): array
    {
        return [
            Operations::CLARO_CONTROLE_BOLETO => self::CONTROLE_BOLETO_TYPE,
            Operations::CLARO_PRE => self::PRE_PAGO_TYPE,
            Operations::CLARO_POS => self::POS_PAGO_TYPE,
            Operations::CLARO_CONTROLE_FACIL => self::CONTROLE_FACIL_TYPE,
        ];
    }
}
