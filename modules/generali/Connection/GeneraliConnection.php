<?php


namespace Generali\Assistance\Connection;

use GA\Connections\GAClient;
use Generali\Adapters\Request\GeneraliActivationRequestAdapter;
use Generali\Connection\Authentication\AuthenticationConnection;
use Generali\Exceptions\GeneraliExceptions;
use Generali\Mail\InsuranceActivation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\HttpClients\Responseable;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;

class GeneraliConnection
{
    protected $saleRepository;
    protected $authenticationConnection;
    protected $gAClient;

    public function __construct(
        SaleRepository $saleRepository,
        AuthenticationConnection $authenticationConnection,
        GAClient $gAClient
    ) {
        $this->saleRepository           = $saleRepository;
        $this->authenticationConnection = $authenticationConnection;
        $this->gAClient                 = $gAClient;
    }

    public function activate(Service $service): JsonResponse
    {
        if ($service->status === ServiceStatus::PENDING_SUBMISSION) {
            $adaptPayload = GeneraliActivationRequestAdapter::adapt($service);
            $response     = $this->gAClient->post(GeneraliRoutes::activate(), $adaptPayload);

            $transaction = data_get($response->toArray(), 'data');

            if ($response->getStatus() === Response::HTTP_CREATED) {
                $this->saleRepository->updateService($service, [
                    'policyId' => data_get($transaction, 'service.license.policyId'),
                    'premium'  => data_get($transaction, 'service.license.premium'),
                    'status'   => data_get($transaction, 'service.status'),
                    'date'     => data_get($transaction, 'service.license.date')
                ]);
            } else {
                throw GeneraliExceptions::serviceNotActivated();
            }

            $this->activationEmailSend($service);

            return response()->json([
                'message' => trans('generali::messages.service_activated')
            ], $response->getStatus());
        }

        throw GeneraliExceptions::incorrectServiceStatus();
    }

    private function activationEmailSend(Service $service)
    {
        return Mail::to($service->customer['email'])->send(new InsuranceActivation($service));
    }

    public function calcPremium(array $attributes): Responseable
    {
        return $this->gAClient->get(GeneraliRoutes::calcPremium(), $attributes);
    }

    public function calcRefund(string $reference): Responseable
    {
         return $this->gAClient->get(GeneraliRoutes::calcRefund(), ['reference' => $reference]);
    }

    public function cancel(string $reference): Responseable
    {
        return $this->gAClient->post(GeneraliRoutes::cancel(), ['reference' => $reference]);
    }

    public function coverage(int $id): Responseable
    {
        return $this->authenticationConnection->auth()->get(GeneraliRoutes::COVERAGE, ['produto_parceiro_plano_id' => $id]);
    }

    public function eligibility(array $deviceAttributes): Responseable
    {
        return $this->gAClient->get(GeneraliRoutes::eligibility(), $deviceAttributes);
    }

    public function getTransactionByReference(string $reference): array
    {
        return $this->gAClient->get(GeneraliRoutes::transactionByReference($reference))->toArray();
    }

    public function plans(array $attributes): Responseable
    {
        return $this->authenticationConnection->auth()->get(GeneraliRoutes::PLAN, [
            'produto_parceiro_id' => data_get($attributes, 'productPartnerId')
        ]);
    }

    public function products(): Responseable
    {
        return $this->authenticationConnection->auth()->get(GeneraliRoutes::PRODUCT);
    }
}
