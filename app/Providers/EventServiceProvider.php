<?php

namespace TradeAppOne\Providers;

use Discount\Events\ImeiUpdateEvent;
use Discount\Listeners\ImeiUpdateLogGenerator;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use TradeAppOne\Features\Customer\CustomerAcquirementListener;
use TradeAppOne\Events\PreAnalysisEvent;

class EventServiceProvider extends ServiceProvider
{
    /** @var string[][] */
    protected $listen = [
        PreAnalysisEvent::class => [
            CustomerAcquirementListener::class
        ],
        ImeiUpdateEvent::class => [
            ImeiUpdateLogGenerator::class
        ]
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
