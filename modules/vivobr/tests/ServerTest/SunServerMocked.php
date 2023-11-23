<?php


namespace VivoBR\Tests\ServerTest;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use VivoBR\Connection\SunRoutes;
use function GuzzleHttp\Psr7\stream_for;

class SunServerMocked
{
    public const CARTAO_PLANS = [1559, 1560, 1561, 1562];

    public function __invoke(RequestInterface $request, array $options)
    {
        $path       = preg_replace('/[^A-z]/', '', $request->getUri()->getPath());
        $params     = $request->getBody()->getContents();
        $method     = $request->getMethod();
        $attributes = json_decode($params, true);

        switch ($path) {
            case SunRoutes::LIST_PLANS:
                $response = $this->mockPlans($attributes);
                break;
            case str_replace('/', '', SunRoutes::TOTALIZATION):
                $response = $this->mockTotalization($request);
                break;
            case SunRoutes::SALES:
                if ($method === 'POST') {
                    $response = $this->mockSales($attributes, $method);
                } else {
                    $listSale = file_get_contents(__DIR__ . '/responses/sales/succesQuery.json');
                    $response = new Response(200, ['Content­Type' => 'application/json'], $listSale);
                }
                break;
            case str_contains($path, SunRoutes::USER):
                $response = $this->mockUser($path, $method);
                break;
            case SunRoutes::PORTABILITY_OPERATORS:
                $response = $this->getPortabilityOperators();
                break;

            default:
                return new Response(200, ['Content­Type' => 'application/json'], '{}');
        }

        return new FulfilledPromise($response);
    }

    private function mockPlans(?array $attributes): Response
    {
        $success = file_get_contents(__DIR__ . '/responses/plans/successPlans.json');
        return new Response(200, ['Content­Type' => 'application/json'], $success);
    }

    public function mockSales(array $attributes, $method): Response
    {
        $userNotFound   = file_get_contents(__DIR__ . '/responses/sales/failureUserNotFound.json');
        $userUnsync     = file_get_contents(__DIR__ . '/responses/sales/failureUserNotFound.json');
        $creditAnalysis = file_get_contents(__DIR__ . '/responses/sales/failureAnaliseCredito.json');

        $cpf = data_get($attributes, 'cpfVendedor');

        switch ($cpf) {
            case SunTestBook::FAILURE_SALESMAN_NOT_FOUND:
                $body     = stream_for($userNotFound);
                $response = new Response(200, ['Content­Type' => 'application/json'], $body);
                break;
            case SunTestBook::FAILURE_SALESMAN_UNSYNC:
                $body     = stream_for($userUnsync);
                $response = new Response(200, ['Content­Type' => 'application/json'], $body);
                break;
            case SunTestBook::FALIURE_CREDITY_ANALYSIS:
                $body     = stream_for($creditAnalysis);
                $response = new Response(200, ['Content­Type' => 'application/json'], $body);
                break;
            case SunTestBook::SALESMAN_PRE_ACTIVATION:
                $response = $this->preActivationResponse($attributes);
                break;
            default:
                $response = $this->choiceSuccess($attributes);
                break;
        }

        return $response;
    }

    private function choiceSuccess($attributes): Response
    {
        if (in_array(data_get($attributes, 'servicos.0.idPlano'), self::CARTAO_PLANS, true)) {
            $success = file_get_contents(__DIR__ . '/responses/sales/successCartaoSale.json');
            $body    = stream_for($success);
        } else {
            $success = file_get_contents(__DIR__ . '/responses/sales/successSale.json');
            $body    = stream_for($success);
        }

        return new Response(200, ['Content­Type' => 'application/json'], $body);
    }

    private function mockUser(?string $path, string $method, ?array $attributes = []): Response
    {
        $getUserFailure = file_get_contents(__DIR__ . '/responses/user/getUserFailure.json');
        $getUserSuccess = file_get_contents(__DIR__ . '/responses/user/getUserSuccess.json');
        $createFailure  = file_get_contents(__DIR__ . '/responses/user/createUserFailure.json');
        $createSuccess  = file_get_contents(__DIR__ . '/responses/user/createUserSuccess.json');
        $updateSuccess  = file_get_contents(__DIR__ . '/responses/user/updateUserSuccess.json');
        $updateFailure  = file_get_contents(__DIR__ . '/responses/user/updateUserFailure.json');

        if (str_contains($path, SunTestBook::FAILURE_SALESMAN_NOT_FOUND)) {
            if ($method === 'GET') {
                $body = $getUserFailure;
            } else {
                $body = $updateFailure;
            }
        } else {
            if ($method === 'GET') {
                $body = $getUserSuccess;
            } else {
                $body = $updateSuccess;
            }
        }

        if ($path === SunRoutes::LIST_PLANS && $method === 'post') {
            $cpf = data_get($attributes, 'cpf');
            switch ($cpf) {
                case SunTestBook::FAILURE_SALESMAN_NOT_FOUND:
                    $body = $createFailure;
                    break;
                default:
                    $body = $createSuccess;
                    break;
            }
        }

        $body = stream_for($body);

        return new Response(200, ['Content­Type' => 'application/json'], $body);
    }

    private function preActivationResponse(array $attributes): Response
    {
        $reprovedIccid       = data_get($attributes, 'servicos.0.iccid', false);
        $preActivation       = data_get($attributes, 'servicos.0.ativarPre', false);
        $preActivationPortIn = data_get($attributes, 'servicos.0.numeroPortabilidade', false);

        if ($reprovedIccid === SunTestBook::INVALID_PRE_ICCID) {
            $fileName = 'preActivationReprovedIccid';
        } elseif ($preActivation && $preActivationPortIn) {
            $fileName = 'preActivationWithPortSuccess';
        } elseif ($preActivation) {
            $fileName = 'preActivationApproved';
        } else {
            return $this->choiceSuccess($attributes);
        }

        $body = stream_for(file_get_contents(__DIR__ . "/responses/sales/{$fileName}.json"));

        return new Response(200, ['Content­Type' => 'application/json'], $body);
    }

    private function getPortabilityOperators(): Response
    {
        $body = stream_for(file_get_contents(__DIR__ . '/responses/sales/portabilityOperators.json'));

        return new Response(200, ['Content­Type' => 'application/json'], $body);
    }

    private function mockTotalization(RequestInterface $request): Response
    {
        $cpf = preg_match('/\d{11}/', $request->getUri()->getPath(), $matches)
            ? $matches[0]
            : [];

        $fileName = ($cpf === SunTestBook::CUSTOMER_TOTALIZATION)
            ? 'successHasTotalization.json'
            : 'successHasNotTotalization.json';

        $body = file_get_contents(__DIR__ . "/responses/api/{$fileName}");

        return new Response(200, ['Content­Type' => 'application/json'], stream_for($body));
    }
}
