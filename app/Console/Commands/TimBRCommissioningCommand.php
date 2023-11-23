<?php

declare(strict_types=1);

namespace TradeAppOne\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use TradeAppOne\Domain\Components\Helpers\Period;
use TimBR\Services\TimBRPremiumRetailCommissioningService;

class TimBRCommissioningCommand extends Command
{
    /** @var string **/
    protected $signature = 'tim:send-premium-retail-commissioning {--initial-date=} {--final-date=} {--force-send}';

    /** @var string **/
    protected $description = 'Command relationated of TIM commissioning';

    /** @var TimBRPremiumRetailCommissioningService **/
    protected $commissioningService;

    public function __construct(TimBRPremiumRetailCommissioningService $commissioningService)
    {
        parent::__construct();
        $this->commissioningService = $commissioningService;
    }

    public function handle(): void
    {
        $this->output->text('Starting process to send sales to tim commissioning..');

        [$startDate, $endDate] = $this->getInitialAndFinalDates();

        $forceSend = empty($this->option('force-send')) ? false : true;

        [
            $withSuccess,
            $withErrors
        ] = $this->commissioningService->sendSalesToCommissioningByRange($startDate, $endDate, $forceSend);

        $this->output->success('Total sales processed with success: ' . $withSuccess);
        $this->output->error('Total sales processed with errors: ' . $withErrors);
    }

    /** @return Carbon[] */
    private function getInitialAndFinalDates(): array
    {
        if (empty($this->option('initial-date'))) {
            return [
                now()->startOfDay(),
                now()->endOfDay()
            ];
        }

        $dates = $this->getValidatedDates();

        return [
            $dates->initialDate->startOfDay(),
            $dates->finalDate->endOfDay()
        ];
    }

    /** @throws InvalidArgumentException */
    private function getValidatedDates(): Period
    {
        $dates = Period::parseFromCommand($this->options(), '!Y-m-d');
        
        if ($dates->finalDate === null) {
            $dates->finalDate = now()->endOfDay();
        }

        if ($dates->initialDate->gt($dates->finalDate)) {
            throw new \InvalidArgumentException('The initial date is greater than final date');
        }
        
        return $dates;
    }
}
