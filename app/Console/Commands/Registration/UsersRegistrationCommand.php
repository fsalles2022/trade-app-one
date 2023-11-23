<?php

namespace TradeAppOne\Console\Commands\Registration;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use TradeAppOne\Domain\Services\Interfaces\UserThirdPartyRepository;
use TradeAppOne\Domain\Services\UserThirdPartyRegistrations\UsersRegistrationCommandService;

class UsersRegistrationCommand extends Command
{
    protected $service;
    protected $signature   = 'user-registration {--operator=} {--method=} {--user=}';
    protected $description = 'Sync users in operators by source';

    public function __construct(UsersRegistrationCommandService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function handle()
    {
        $options = $this->options();
        try {
            $resume = $this->service->process($options);
            $this->getResume($resume);
        } catch (\ErrorException $exception) {
            $this->error('Something went wrong! ' . $exception->getMessage() . $exception->getLine() . $exception->getFile());
        }
        $resume = $this->service->process($options);
        $this->getResume($resume);
    }

    public function getResume(Collection $resume)
    {
        $success = $resume->where('status', true)->count();
        $error   = $resume->where('status', false)->count();
        $created = $resume->where('action', UserThirdPartyRepository::CREATED)->count();
        $synced  = $resume->where('action', UserThirdPartyRepository::UPDATED)->count();
        $this->info('success ' . $success);
        $this->info('created ' . $created);
        $this->info('synced ' . $synced);
        $this->info('error ' . $error);
    }
}
