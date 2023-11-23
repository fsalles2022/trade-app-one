<?php

namespace TradeAppOne\Console\Commands\Registration;

use Illuminate\Console\Command;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Repositories\Collections\NetworkRepository;
use TradeAppOne\Exports\Operators\RegisterMailToNextel;

class RegisterMailToNextelCommand extends Command
{
    protected $signature   = 'mail:nextel {--networks=*} {--all}';
    protected $description = 'Send email to operator by network or all';

    public function handle(): void
    {
        $networks = $this->option('all')
            ? NetworkRepository::networksByOperator(Operations::NEXTEL)
                ->whereNotIn('slug', ['claro','tradeup-group','rede-teste'])->pluck('slug')
            : $this->option('networks');

        foreach ($networks as $network) {
            (new RegisterMailToNextel())->build($network);
        }

        $this->info(trans('messages.dispatch_mail'));
    }
}
