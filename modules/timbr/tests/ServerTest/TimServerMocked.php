<?php


namespace TimBR\Tests\ServerTest;

use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\RejectedPromise;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Str;
use Psr\Http\Message\RequestInterface;
use TimBR\Connection\TimBRRoutes;
use TimBR\Tests\TimBRTestBook;
use function GuzzleHttp\Psr7\stream_for;

class TimServerMocked
{
    public function __invoke(RequestInterface $req, array $options)
    {
        $path                             = $req->getUri()->getPath();
        $attributes                       = json_decode($req->getBody()->getContents(), true);
        $domains                          = file_get_contents(__DIR__ . '/domains.json');
        $controleFaturaEligibilitySucess  = file_get_contents(__DIR__ . '/controleFaturaEligibilitySucess.json');
        $controleFlexEligibilitySucess    = file_get_contents(__DIR__ . '/controleFlexEligibilitySucess.json');
        $controleExpressEligibilitySucess = file_get_contents(__DIR__ . '/controleExpressEligibilitySucess.json');
        $blackEligibilitySuccess          = file_get_contents(__DIR__ . '/blackEligibilitySuccess.json');
        $blackExpressEligibilitySuccess   = file_get_contents(__DIR__ . '/blackExpressEligibilitySuccess.json');
        $blackMultiEligibilitySuccess     = file_get_contents(__DIR__ . '/blackMultiEligibilitySuccess.json');

        if ($path == TimBRRoutes::ELIGIBILITY) {
            $caseCpf = data_get($attributes, 'customer.socialSecNo');
            if ($caseCpf == TimBRTestBook::SUCCESS_CUSTOMER) {
                $body = stream_for($controleFaturaEligibilitySucess);
            } elseif ($caseCpf == TimBRTestBook::SUCCESS_CUSTOMER_EXPRESS) {
                $body = stream_for($controleExpressEligibilitySucess);
            } elseif ($caseCpf == TimBRTestBook::SUCCESS_CUSTOMER_FLEX) {
                $body = stream_for($controleFlexEligibilitySucess);
            } elseif ($caseCpf == TimBRTestBook::SUCCESS_CUSTOMER_BLACK) {
                $body = stream_for($blackEligibilitySuccess);
            } elseif ($caseCpf == TimBRTestBook::SUCCESS_CUSTOMER_BLACK_EXPRESS) {
                $body = stream_for($blackExpressEligibilitySuccess);
            } elseif ($caseCpf == TimBRTestBook::SUCCESS_CUSTOMER_BLACK_MULTI) {
                $body = stream_for($blackMultiEligibilitySuccess);
            } elseif ($caseCpf == TimBRTestBook::FAILURE_CUSTOMER_ELIGIBILITY) {
                $body     = stream_for('{
                    "type" : "error", 
                    "status" : "500", 
                    "internalCode" : "-31008", 
                    "message" : "Cliente Perfil prepago", 
                    "transactionId" : "Id-891e885b9539bb833c351a1b"
                }');
                $response = new Response(406, ['Content­Type' => 'application/json'], $body);
                return new RejectedPromise(new ServerException('Server is down', $req, $response));
            } else {
                $body = stream_for($controleFaturaEligibilitySucess);
            }
        }

        if (Str::contains($path, 'status')) {
            $body = stream_for('{"order": [{"protocol": "123","status": "Concluído"}]}');
        }

        if (Str::contains($path, 'address')) {
            if ($req->getUri()->getQuery() === 'postCode=20021010') {
                $body = stream_for(
                    '{
                        "postCode":"20021010",
                        "streetType":"AVENIDA",
                        "streetName":"ALMIRANTE SILVIO DE NORONHA",
                        "locality":"CENTRO",
                        "city":"RIO DE JANEIRO",
                        "stateOrProvince":"RJ",
                        "country":"Brasil",
                        "addressType":"Logradouro",
                        "codeStreet":270446,
                        "ddd":"21",
                        "cnl":"21000"
                    }'
                );
            } else {
                $body = stream_for(
                    '{ 
                        "postCode" : "06454000", 
                        "streetType" : "ALAMEDA", 
                        "streetName" : "RIO NEGRO", 
                        "locality" : "ALPHAVILLE", 
                        "city" : "BARUERI", 
                        "stateOrProvince" : "SP"
                    }'
                );
            }
        }

        if ($path == TimBRRoutes::DOMAINS) {
            $body = stream_for($domains);
        }

        if ($path == TimBRRoutes::ORDER) {
            $caseCpf = data_get($attributes, 'order.customer.socialSecNo');
            if ($caseCpf == TimBRTestBook::SUCCESS_CUSTOMER || $caseCpf == TimBRTestBook::SUCCESS_CUSTOMER_EXPRESS) {
                $body = stream_for('{"order": {"protocol": "2018231961631","contract": {"msisdn": "11876806060"}}}');
            } else {
                $body     = stream_for('{
                    "type" : "error", 
                    "status" : "500", 
                    "internalCode" : "-31008", 
                    "message" : "Nao foi possivel realizar a ativacao do numero neste chip. Tente novamente com um novo chip", 
                    "transactionId" : "Id-891e885b9539bb833c351a1b"
                }');
                $response = new Response(500, ['Content­Type' => 'application/json'], $body);
                return new RejectedPromise(new ServerException($body, $req, $response));
            }
        }

        if ($path === TimBRRoutes::TRANSACTION_TOKEN) {
            $body = stream_for(
                '{"transactionToken": "string"}'
            );
        }

        $matches = [];

        preg_match( '#^' . preg_replace(['/\//', '/\{\{MSISDN\}\}/'], ['\/', '.*'], TimBRRoutes::CUSTOMER_NUMBER_VALIDATION) . '$#', $path, $matches);

        if (isset($matches[0]) && ! empty($matches[0])) {
            $body = stream_for(
                '{"validated": "true"}'
            );
        }

        if (str_contains($path, TimBRRoutes::ENCRYPT_CPF)) {
            $body = stream_for(
                '{"encrypted": "abcdef12345"}'
            );
        }

        if (str_contains($path, TimBRRoutes::ORDER_APPROVAL)) {
            $body = stream_for('
                {
                    "reason": {
                        "status": "OK",
                        "reasonCode": "0",
                        "description": "Cliente Aprovado na Consulta 3"
                    }
                }
            ');
        }

        if (str_contains($path, TimBRRoutes::SIMCARD_ACTIVATION)) {
            $body = stream_for('
                {
                  "transactionStatus": "0",
                  "transactionStatusControl": "Success",
                  "device": {
                    "iccid": "89550000000000000000",
                    "imsi": "123456",
                    "msisdn": "11957412548"
                  }
                }
            ');
        }

        if (str_contains($path, TimBRRoutes::GENERATE_PROTOCOL)) {
            $body = stream_for('
                {
                    "interactionProtocol": "2023523004513"
                }
            ');
        }

        return new FulfilledPromise(
            new Response(200, ['Content­Type' => 'application/json'], $body)
        );
    }
}
