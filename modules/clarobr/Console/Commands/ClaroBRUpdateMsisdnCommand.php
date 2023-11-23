<?php

namespace ClaroBR\Console\Commands;

use ClaroBR\Services\UpdateAttributes\ClaroBRUpdateMsisdnService;
use Illuminate\Console\Command;

class ClaroBRUpdateMsisdnCommand extends Command
{
    public const MODE_ALL = 'all';

    protected $signature = UpdateAttributes::GROUP_COMMAND . ":msisdn {--M|mode=".self::MODE_ALL."} {--I|initial=}";

    public function handle()
    {
        $service = resolve(ClaroBRUpdateMsisdnService::class);
        $result  = $service->update($this->options());

        logger()->info(
            'ClaroBRUpdateMsisdnCommand, atributos atualizados - MSISDN',
            [
                'alterados' => $result->count(),
                'vendas' => $result->pluck('serviceTransaction')->toArray()
            ]
        );
    }
}
