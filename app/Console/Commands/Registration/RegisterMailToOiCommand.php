<?php

namespace TradeAppOne\Console\Commands\Registration;

use Illuminate\Console\Command;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Repositories\Collections\NetworkRepository;
use TradeAppOne\Exports\Operators\RegisterMailToOi;

class RegisterMailToOiCommand extends Command
{
    protected $signature   = 'mail:oi {--networks=*} {--all}';
    protected $description = 'Send email to operator by network or all';

    public function handle(): void
    {
        $networks = $this->option('all')
            ? NetworkRepository::networksByOperator(Operations::OI)->pluck('slug')
            : $this->option('networks');

        foreach ($networks as $network) {
            (new RegisterMailToOi())->build($network);
        }

        $this->info(trans('messages.dispatch_mail'));
    }
}
