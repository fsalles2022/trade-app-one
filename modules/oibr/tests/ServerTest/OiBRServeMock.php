<?php

namespace OiBR\Tests\ServerTest;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Response;
use OiBR\Connection\OiBRRoutes;
use Psr\Http\Message\RequestInterface;
use function GuzzleHttp\Psr7\stream_for;

class OiBRServeMock
{
    public function __invoke(RequestInterface $req, array $options)
    {
        $path       = $req->getUri()->getPath();
        $params     = $req->getBody()->getContents();
        $method     = $req->getMethod();
        $attributes = json_decode($params, true);


        $successControleCartao            = file_get_contents(__DIR__ . '/responses/plans/successControleBoletoPlans.json');
        $successControleBoleto            = file_get_contents(__DIR__ . '/responses/plans/successControleBoletoPlans.json');
        $successEligibilityControleBoleto = file_get_contents(__DIR__ . '/responses/eligibility/successControleBoleto.json');
        $successEligibilityControleCartao = file_get_contents(__DIR__ . '/responses/eligibility/successControleBoleto.json');
        $registerCreditCard               = file_get_contents(__DIR__ . '/responses/creditcard/registerCreditCard.json');
        $successControleCartaoActivation  = file_get_contents(__DIR__ . '/responses/activation/queryStatusCartaoActivation.json');
        $successControleCartaoMigration   = file_get_contents(__DIR__ . '/responses/activation/successControleCartaoMigration.json');

        switch ($path) {
            case OiBRRoutes::getPlans('boleto_bancario'):
                $body = stream_for($successControleBoleto);
                break;
            case OiBRRoutes::getPlans('cartao_credito'):
                $body = stream_for($successControleCartao);
                break;
            case str_contains($path, 'adesao-boleto/v1/msisdn/'):
                $body = stream_for($successEligibilityControleBoleto);
                break;
            case str_contains($path, 'oicontrole/rs/v1/contrato/elegibilidade'):
                $body = stream_for($successEligibilityControleCartao);
                break;
            case OiBRRoutes::REGISTER_CREDIT_CARD:
                $body = stream_for($registerCreditCard);
                break;
            case str_contains($path, 'oicontrole/cartao/adesao/v1/'):
                if ($method == 'POST') {
                    $body = stream_for('{}');
                    return new FulfilledPromise(
                        new Response(202, ['Content­Type' => 'application/json'], $body)
                    );
                } else {
                    $body = stream_for($successControleCartaoActivation);
                }
                break;
            case OiBRRoutes::postControleCartaoMigration():
                $body =stream_for($successControleCartaoMigration);
                break;
            default:
                $body = stream_for('{}');
                return new FulfilledPromise(
                    new Response(201, ['Content­Type' => 'application/json'], $body)
                );
                break;
        }

        return new FulfilledPromise(
            new Response(200, ['Content­Type' => 'application/json'], $body)
        );
    }
}
