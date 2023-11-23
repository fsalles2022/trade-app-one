<?php

namespace TimBR\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use League\Csv\Writer;
use TimBR\Http\Requests\CepFormRequest;
use TimBR\Http\Requests\CheckMasterMsisdnFormRequest;
use TimBR\Http\Requests\CreditAnalysisFormRequest;
use TimBR\Http\Requests\EligibilityFormRequest;
use TimBR\Http\Requests\RebateImportRequest;
use TimBR\Http\Requests\TimBRRegisterCreditCardFormRequest;
use TimBR\Http\Requests\TimServiceTransactionFormRequest;
use TimBR\Services\TimBRService;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;
use TradeAppOne\Domain\Enumerators\Importables;
use TradeAppOne\Domain\Importables\ImportableFactory;
use TradeAppOne\Domain\Importables\ImportEngine;
use TradeAppOne\Domain\Importables\TimBRRebateImportable;
use TradeAppOne\Http\Controllers\Controller;

class TimBRController extends Controller
{
    protected $service;

    public function __construct(TimBRService $service)
    {
        $this->service = $service;
    }

    public function domains(Request $request): Collection
    {
        $user = Auth::user();
        return $this->service->getDomains($user, $request);
    }

    public function eligibility(EligibilityFormRequest $request): JsonResponse
    {
        $user = Auth::user();
        return $this->service->eligibility($user, $request->pointOfSale, $request->all())->adapt();
    }

    public function checkMasterMsisdn(CheckMasterMsisdnFormRequest $request): JsonResponse
    {
        return $this->service->checkMasterMsisdn(Auth::user(), $request->input('pointOfSaleId'), $request->input('masterMsisdn'), $request->all())->adapt();
    }

    public function creditAnalysis(CreditAnalysisFormRequest $request): JsonResponse
    {
        return $this->service->orderApproval(Auth::user(), $request->input('pointOfSaleId'), $request->all())->adapt();
    }

    public function simCardActivation(TimServiceTransactionFormRequest $request): JsonResponse
    {
        return $this->service->simCardActivation(Auth::user(), $request->all())->adapt();
    }

    public function cep(CepFormRequest $request): JsonResponse
    {
        $user          = Auth::user();
        $requestParams = $request->validated();
        $cep           = data_get($requestParams, 'cep', 0);
        return $this->service->cep($user, $cep)->adapt();
    }

    public function postRegisterCreditCard(TimBRRegisterCreditCardFormRequest $request): JsonResponse
    {
        return $this->service->registerCreditCard($request->all())->adapt();
    }


    public function processRebateImportableAction(RebateImportRequest $request)
    {
        $file = $request->file('file');

        /** @var TimBRRebateImportable $importable */
        $importable = ImportableFactory::make(Importables::TIM_REBATE);

        $contents = CsvHelper::fileToCsv($file);

        try {
            $importable->startProcess();
            $importable->setTimProductToImport($contents[4] ?? []);

            $engine = new ImportEngine($importable);
            $errors = $engine->process($file, 7);

            if ($errors) {
                $importable->endProcessWithErrors();

                return $errors;
            }

            $importable->endProcessWithSuccess();

            $this->response['message'] = trans('messages.default_success');

            return response()->json($this->response, Response::HTTP_CREATED);
        } catch (\Throwable $exception) {
            $importable->endProcessWithErrors();

            $this->response['message'] = trans('messages.default');

            return response()->json($this->response, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function getRebateImportableExampleAction(Request $request): Writer
    {
        return TimBRRebateImportable::buildExample();
    }
}
