<?php

namespace ClaroBR\Console\Commands;

use ClaroBR\Services\UpdateAttributes\ClaroBRUpdateDependentsService;
use Illuminate\Console\Command;

class ClaroBRUpdateDependentsCommand extends Command
{
    protected $signature = UpdateAttributes::GROUP_COMMAND . ':dependents {--S|serviceTransaction=} {--N|network=*}';

    public function handle()
    {
        $service = resolve(ClaroBRUpdateDependentsService::class);
        $result  = $service->update($this->options());
        $this->info($result->count() . " alterados");
        logger()->info(
            'atributos atualizados - dependents',
            ['vendas' => $result->pluck('serviceTransaction')->toArray()]
        );
    }
}
