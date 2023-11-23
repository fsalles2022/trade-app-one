<?php

namespace NextelBR\Tests\ServerTest;

use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\RejectedPromise;
use GuzzleHttp\Psr7\Response;
use NextelBR\Connection\NextelBR\NextelBRRoutes;
use NextelBR\Tests\NextelBRTestBook;
use Psr\Http\Message\RequestInterface;
use function GuzzleHttp\Psr7\stream_for;

class NextelBRServeMock
{
    public function __invoke(RequestInterface $req, array $options)
    {
        $path       = $req->getUri()->getPath();
        $params     = $req->getBody()->getContents();
        $attributes = json_decode($params, true);


        $successEligibility  = file_get_contents(__DIR__ . '/responses/eligibilityResponses/successEligibility.json');
        $successEligibilityA = file_get_contents(__DIR__ . '/responses/eligibilityResponses/successEligibilityFailurePreAdhesion.json');
        $successPreAdhesion  = file_get_contents(__DIR__ . '/responses/preAdhesionResponses/successPreAdhesion.json');
        $banks               = file_get_contents(__DIR__ . '/responses/domains/bancos.json');
        $operators           = file_get_contents(__DIR__ . '/responses/domains/operadoras.json');
        $dueDates            = file_get_contents(__DIR__ . '/responses/domains/datasPagamento.json');
        $address             = file_get_contents(__DIR__ . '/responses/domains/cep.json');
        $plans               = file_get_contents(__DIR__ . '/responses/plans/successPlans.json');
        $successAdhesion     = file_get_contents(__DIR__ . '/responses/adhesion/success.json');
        $failureAdhesion     = file_get_contents(__DIR__ . '/responses/adhesion/failure.json');
        $failurePreAdhesion  = file_get_contents(__DIR__ . '/responses/preAdhesionResponses/failurePreAdhesion.json');
        $bankDataInvalid     = file_get_contents(__DIR__ . '/responses/validationBankData/bankDataInvalid.json');

        switch ($path) {
            case NextelBRRoutes::eligibility():
                if (data_get($attributes, 'cpf') == NextelBRTestBook::SUCCESS_CUSTOMER) {
                    $body = stream_for($successEligibility);
                } else {
                    $body     = stream_for($successEligibilityA);
                    $response = new Response(412, ['Content­Type' => 'application/json'], $body);
                    return new RejectedPromise(new ClientException('Server is down', $req, $response));
                }
                break;
            case NextelBRRoutes::portabilityDates():
                $body = stream_for($this->generatePortabilityDates());
                break;
            case NextelBRRoutes::banks():
                $body = stream_for($banks);
                break;
            case NextelBRRoutes::portabilityOperators():
                $body = stream_for($operators);
                break;
            case NextelBRRoutes::paymentDates():
                $body = stream_for($dueDates);
                break;
            case NextelBRRoutes::products():
                $body = stream_for($plans);
                break;
            case NextelBRRoutes::preAdhesion(NextelBRTestBook::SUCCESS_PROTOCOL):
                if (data_get($attributes, 'cpf') == NextelBRTestBook::SUCCESS_CUSTOMER) {
                    $body = stream_for($successPreAdhesion);
                } else {
                    $body     = stream_for($failurePreAdhesion);
                    $response = new Response(406, ['Content­Type' => 'application/json'], $body);
                    return new RejectedPromise(new ClientException('Server is down', $req, $response));
                }
                break;
            case NextelBRRoutes::preAdhesion(NextelBRTestBook::FAILURE_PROTOCOL):
                $body = stream_for($failureAdhesion);
                break;
            case NextelBRRoutes::adhesion(NextelBRTestBook::SUCCESS_PROTOCOL):
                $body = stream_for($successAdhesion);
                break;
            case NextelBRRoutes::cep(NextelBRTestBook::SUCCESS_CEP):
                $body = stream_for($address);
                break;
            case NextelBRRoutes::validateBankData():
                if (data_get($attributes, 'id_banco') == NextelBRTestBook::ID_BANK_DATA_INVALID) {
                    $body     = stream_for($bankDataInvalid);
                    $response = new Response(412, ['Content­Type' => 'application/json'], $body);
                    return new RejectedPromise(new ClientException('Precondition Failed', $req, $response));
                }

                $body = stream_for('{}');
                break;
            default:
                $body = stream_for('{}');
                break;
        }

        return new FulfilledPromise(
            new Response(200, ['Content­Type' => 'application/json'], $body)
        );
    }

    private function generatePortabilityDates(): string
    {
        $date                        = Carbon::now();
        $week                        = $date->addWeek();
        $dates['datasPortabilidade'] = [];
        for ($date = Carbon::now(); $date < $week; $date->addDay()) {
            $dates['datasPortabilidade'][] = $date->format('Y-m-d');
        }

        return json_encode($dates);
    }
}
