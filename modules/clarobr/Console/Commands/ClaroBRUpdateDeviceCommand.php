<?php

namespace ClaroBR\Console\Commands;

use ClaroBR\Services\UpdateAttributes\ClaroBRUpdateDependentsService;
use ClaroBR\Services\UpdateAttributes\ClaroBRUpdateDeviceService;
use Illuminate\Console\Command;

class ClaroBRUpdateDeviceCommand extends Command
{
    protected $signature = UpdateAttributes::GROUP_COMMAND . ':device {--S|serviceTransaction=} {--N|network=*}';

    public function handle()
    {
        $service = resolve(ClaroBRUpdateDeviceService::class);
        $result  = $service->update($this->options());
        $this->info($result->count() . " alterados");
        logger()->info(
            'atributos atualizados - device',
            ['vendas' => $result->pluck('serviceTransaction')->toArray()]
        );
    }
}
