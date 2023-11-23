<?php


namespace Generali\Services\Queue;

use Carbon\Carbon;
use Gateway\Services\GatewayService;
use Generali\DTO\InsuranceUpdateDTO;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;

class InsuranceUpdateQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Collection
     */
    protected $insurers;

    public function __construct(array $insurers)
    {
        $this->insurers = $this->mountInsurers($insurers);
    }

    public function handle(SaleRepository $saleRepository, GatewayService $gateway): void
    {
        $this->insurers->each(function (InsuranceUpdateDTO $insurance) use (&$saleRepository, &$gateway) {
            $service = $saleRepository->findInSale($insurance->reference);
            if ($service !== null) {
                $premium           = data_get($service, 'premium', []);
                $extras            = $this->mountExtrasToMongo($insurance->extra);
                $premiumWithExtras = ['premium' => array_merge($premium, $extras)];
                $toUpdated         = array_merge($premiumWithExtras, ['status' => $insurance->status]);
                $serviceUpdated    = $saleRepository->updateService($service, $toUpdated);

                if ($serviceUpdated && $insurance->status === ServiceStatus::CANCELED) {
                    $refundValue = (string) data_get($extras, 'refund.refundedValue');
                    $refundValue = str_replace('.', '', $refundValue);
                    $gateway->cancel($serviceUpdated, $refundValue);
                }
            }
        });
    }

    private function mountInsurers(array $insurers): Collection
    {
        $items = collect([]);
        foreach ($insurers as $insurance) {
            $reference = data_get($insurance, 'reference', '');
            $status    = data_get($insurance, 'status', '');
            $extra     = data_get($insurance, 'extra', []);
            $items->push(new InsuranceUpdateDTO($reference, $status, $extra));
        }
        return $items;
    }

    private function mountExtrasToMongo(array $extras): array
    {
        $refundValue = data_get($extras, 'valor_estorno', 0.00);
        $policyId    = data_get($extras, 'num_apolice', null);
        $cancelDate  = data_get($extras, 'data_cancelamento', null);

        return ['refund' => [
                'cancelDate' => Carbon::parse($cancelDate)->toIso8601String(),
                'refundedValue' => (double) $refundValue,
                'policyId' => $policyId
            ]
        ];
    }
}
