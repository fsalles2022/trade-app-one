<?php

declare(strict_types=1);

namespace ClaroBR\Services;

use ClaroBR\Adapters\Siv3CheckAuthorizationCode;
use ClaroBR\Adapters\Siv3SendAuthorization;
use ClaroBR\Connection\Siv3Connection;
use ClaroBR\Exceptions\Siv3Exceptions;

class Siv3Service
{
    /** @var Siv3Connection */
    private $siv3Connection;

    public function __construct(Siv3Connection $siv3Connection)
    {
        $this->siv3Connection = $siv3Connection;
    }

    /**
     * @param mixed[] $attributes
     * @return mixed[]
     */
    public function sendDataTocheckAuthorization(array $attributes): array
    {
        $response = $this->siv3Connection->sendAuthorization(
            new Siv3SendAuthorization(
                $attributes['customer']['phoneNumber'] ?? null,
                $attributes['origin'] ?? null,
                $attributes['type'] ?? null
            )
        );

        throw_unless($response->isSuccess(), Siv3Exceptions::unavailableService());

        return $response->toArray();
    }

    /**
     * @param mixed[] $attributes
     * @return mixed[]
     */
    public function checkAuthorizationCode(array $attributes): array
    {
        $response = $this->siv3Connection->checkAuthorizationCode(
            new Siv3CheckAuthorizationCode(
                $attributes['phoneNumber'] ?? null,
                $attributes['code'] ?? null
            )
        );

        throw_unless($response->isSuccess(), Siv3Exceptions::unavailableService());

        throw_if($response->get('success', false) === false, Siv3Exceptions::invalidCode());

        return $response->toArray();
    }
}
