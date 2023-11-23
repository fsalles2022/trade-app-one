<?php

namespace VivoBR\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;
use VivoBR\Services\UserRegistrationVivoService;

class VivoSyncUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    protected $user;
    protected $pointOfSale;

    public function __construct(User $user, PointOfSale $pointOfSale)
    {
        $this->pointOfSale = $pointOfSale;
        $this->user        = $user;
    }

    public function handle(UserRegistrationVivoService $service)
    {
        $service->runOneInAPI($this->user, $this->pointOfSale);
    }
}
