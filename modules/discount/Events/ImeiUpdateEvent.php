<?php

declare(strict_types=1);

namespace Discount\Events;

use Discount\Services\Input\Input;
use TradeAppOne\Domain\Models\Collections\Service;

class ImeiUpdateEvent
{
    /** @var Service|null */
    private $service;

    /** @var Input|null */
    private $input;

    public function __construct(?Service $service, ?Input $input)
    {
        $this->service = $service;
        $this->input   = $input;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function getInput(): ?Input
    {
        return $this->input;
    }
}
