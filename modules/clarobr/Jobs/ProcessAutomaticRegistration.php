<?php

declare(strict_types=1);

namespace ClaroBR\Jobs;

use ClaroBR\Exceptions\SivInvalidCredentialsException;
use ClaroBR\Services\SivAutomaticRegistrationService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Throwable;
use TradeAppOne\Exceptions\BusinessExceptions\PointOfSaleNotFoundException;

class ProcessAutomaticRegistration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var int */
    public $tries = 3;

    /** @var mixed[] */
    protected $data;

    /** @param mixed[] $requestParams */
    public function __construct(array $requestParams)
    {
        $this->data = $requestParams;
    }

    /**
     * @throws Throwable
     * @throws SivInvalidCredentialsException
     * @throws PointOfSaleNotFoundException
     */
    public function handle(SivAutomaticRegistrationService $automaticRegistrationService)
    {
        $automaticRegistrationService->automaticRegistration($this->data);
    }
}
