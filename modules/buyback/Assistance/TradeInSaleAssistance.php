<?php

namespace Buyback\Assistance;

use Buyback\Enumerators\EvaluationStatus;
use Buyback\Exceptions\TradeInExceptions;
use Buyback\Exceptions\GeneratorVoucherException;
use Buyback\Exceptions\RevaluationAlreadyDoneException;
use Buyback\Resources\contracts\VoucherLayout;
use Buyback\Services\TradeInService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Components\Helpers\MongoDateHelper;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Services\AssistanceBehavior;
use TradeAppOne\Domain\Services\Cancel\CancelService;
use TradeAppOne\Domain\Services\SaleService;

class TradeInSaleAssistance implements AssistanceBehavior
{
    use CancelService;

    private $saleService;
    private $tradeInService;

    public function __construct(SaleService $saleService, TradeInService $tradeInService)
    {
        $this->saleService    = $saleService;
        $this->tradeInService = $tradeInService;
    }

    public function integrateService(Service $service, array $payload = []):JsonResponse
    {
        $this->saleService->pushLogService($service, $payload);

        throw_if($service->isCanceled(), TradeInExceptions::voucherAlreadyCanceled());

        $this->saleService->updateStatusService($service, ServiceStatus::ACCEPTED);

        return response()->json([
            'message' => trans('buyback::messages.evaluation_success'),
            'data' => $service,
        ], Response::HTTP_CREATED);
    }

    public function revaluation(array $parameters): Service
    {
        $user           = Auth::user();
        $questions      = Arr::get($parameters, 'questions');
        $evaluationType = Arr::get($parameters, 'evaluationType') ?? EvaluationStatus::APPRAISER;

        $service = $this->saleService->findService(Arr::get($parameters, 'serviceTransaction', ''));

        if ($service !== null && $service->status !== ServiceStatus::ACCEPTED) {
            throw new RevaluationAlreadyDoneException();
        }

        $deviceEvaluation = $this->tradeInService->mountEvaluationFromService($service, $questions)->toArray();

        

        $deviceEvaluation['user'] = $user->toMongoAggregation();
        $attributesToUpdate       = $this->adaptEvaluationToUpdate($deviceEvaluation, $service->price, $evaluationType);

        return $this->saleService->updateService($service, $attributesToUpdate);
    }

    private function adaptEvaluationToUpdate(array $deviceEvaluation, float $servicePrice, string $evaluationType): array
    {
        return [
            "evaluations.{$evaluationType}"   => $deviceEvaluation,
            'status'                          => EvaluationStatus::EVALUATION_STATUS_BY_TYPE[$evaluationType],
            'price'                           => EvaluationStatus::APPRAISER === $evaluationType ? $deviceEvaluation['price']: $servicePrice,
            "evaluations.{$evaluationType}.createdAt" => MongoDateHelper::dateTimeToUtc(Carbon::now())
        ];
    }

    public function canGenerateVoucher(string $serviceTransaction)
    {
        $service = $this->saleService->findService($serviceTransaction);

        $this->serviceNotNull($service)->serviceNotCanceled($service)->serviceIsAccepted($service);

        return $service;
    }

    public function produceVoucher(Service $service): ?string
    {
        try {
            return (new VoucherLayout($service->toArray(), $service->sale))->toPdf();
        } catch (\Exception $exception) {
            throw new GeneratorVoucherException();
        }
    }

    public function burnVoucher(string $serviceTransaction)
    {
        $service = $this->saleService->findService($serviceTransaction);

        $this->validateService($service);

        return $this->setStatusVoucher($service, $serviceTransaction, ServiceStatus::APPROVED);
    }

    private function validateService($service): void
    {
        $this->serviceNotNull($service)
            ->serviceHasOperations($service, [Operations::IPLACE, Operations::IPLACE_ANDROID, Operations::IPLACE_IPAD])
            ->serviceNotCanceled($service)
            ->serviceIsAccepted($service);
    }

    public function setStatusVoucher(Service $service, string $serviceTransaction, string $serviceStatus)
    {
        $log = ['serviceTransaction' => $serviceTransaction, 'status' => $serviceStatus];

        $this->saleService->pushLogService($service, $log);

        return $this->saleService->updateStatusService($service, $serviceStatus);
    }

    private function serviceHasOperations(Service $service, array $operations): TradeInSaleAssistance
    {
        if ($service->operationIs($operations)) {
            return $this;
        }

        throw TradeInExceptions::voucherNotBelongsToOperation($service->operation);
    }
}
