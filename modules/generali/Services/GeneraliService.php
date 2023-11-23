<?php


namespace Generali\Services;

use Generali\Adapters\GeneraliAdapterPlan;
use Generali\Assistance\Connection\GeneraliConnection;
use Generali\Exceptions\GeneraliExceptions;
use Generali\resources\contracts\InsuranceTicketTemplate;
use Generali\Services\Queue\InsuranceUpdateQueue;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Http\Controllers\Controller;

class GeneraliService extends Controller
{
    protected $saleService;
    protected $generaliConnection;

    public function __construct(SaleService $saleService, GeneraliConnection $generaliConnection)
    {
        $this->saleService        = $saleService;
        $this->generaliConnection = $generaliConnection;
    }

    public function calcRefund(string $reference): array
    {
        $response            = $this->generaliConnection->calcRefund($reference)->get('data');
        $response['message'] = trans('generali::messages.confirm_refund');

        return $response;
    }

    public function coverage(int $id): array
    {
        return $this->generaliConnection->coverage($id)->toArray();
    }

    public function eligibility(array $deviceAttributes): array
    {
        $response = $this->generaliConnection->eligibility($deviceAttributes)->toArray();
        return GeneraliAdapterPlan::run($response, $deviceAttributes);
    }

    public function insuranceTicket(string $serviceTransaction): ?string
    {
        try {
            $service = $this->saleService->findService($serviceTransaction);
            return (new InsuranceTicketTemplate($service))->layout()->toPdf();
        } catch (\Exception $exception) {
            throw GeneraliExceptions::insuranceTicketNotCreated($exception);
        }
    }

    public function plans(array $attributes): array
    {
        return $this->generaliConnection->plans($attributes)->toArray();
    }

    public function products(): array
    {
        return $this->generaliConnection->products()->toArray();
    }

    public function updateInsuranceFromGateway(array $requestValidated): void
    {
        $insurers = data_get($requestValidated, 'insurers', []);
        InsuranceUpdateQueue::dispatch($insurers);
    }
}
