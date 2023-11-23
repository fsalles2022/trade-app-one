<?php

declare(strict_types=1);

namespace Reports\SubModules\Core\Models;

use TradeAppOne\Domain\Enumerators\Operations;

class Operators
{
    /** @var string[] */
    protected $operators;

    /** @param string[] $operators */
    public function __construct(array $operators)
    {
        $this->operators = $operators;
    }

    /** @return string[] */
    public function all(): array
    {
        return $this->operators;
    }

    /** @return string[] */
    public function getTelecommunicationOperators(): array
    {
        return array_filter(
            $this->operators,
            function (string $operator): bool {
                return in_array($operator, array_keys(Operations::TELECOMMUNICATION_OPERATORS));
            }
        );
    }

    /** @return string[] */
    public function getSecurityOperators(): array
    {
        return array_filter(
            $this->operators,
            function (string $operator): bool {
                return in_array($operator, array_keys(Operations::SECURITY_OPERATORS));
            }
        );
    }
}
