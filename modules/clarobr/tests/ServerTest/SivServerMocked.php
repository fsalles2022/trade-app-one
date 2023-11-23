<?php


namespace ClaroBR\Tests\ServerTest;

use ClaroBR\Connection\SivRoutes;
use ClaroBR\Tests\ClaroBRTestBook;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Psr\Http\Message\RequestInterface;
use function GuzzleHttp\Psr7\stream_for;

class SivServerMocked
{
    private $path;
    private $params;
    private $query;
    private $loggedUser;
    private $method;

    public function __invoke(RequestInterface $req, array $options)
    {
        $this->path       = $req->getUri()->getPath();
        $this->params     = $req->getBody()->getContents();
        $this->query      = $req->getUri()->getQuery();
        $this->method     = $req->getMethod();
        $this->loggedUser = Auth::user();

        $routes = $this->sivServerRoutes();

        $status = 200;

        $body = array_key_exists($this->path, $routes)
            ? $routes[$this->path]
            : stream_for('{}');

        if (($this->method === 'PUT') && preg_match('/^' . SivRoutes::ENDPOINT_SALES . '\/*/', $this->path)) {
            if ($this->matchIn($this->params, ClaroBRTestBook::ICCID_WITH_ERROR_UPDATE)) {
                $body   = $this->fetchFile(ClaroBRResponseBook::UPDATE_ERROR);
                $status = 422;
            } else {
                $this->fetchFile(ClaroBRResponseBook::UPDATE_SUCCESS);
            }
        }

        if (preg_match('/^' . SivRoutes::ENDPOINT_USER . '\/*/', $this->path)) {
            $body = $this->fetchFile(ClaroBRResponseBook::USER_REGISTER);
        }

        if (preg_match('/^' . SivRoutes::ENDPOINT_USER . "\/pdv*/", $this->path)) {
            $body = $this->fetchFile(ClaroBRResponseBook::PDV_REGISTER);
        }

        if (preg_match('/^'.SivRoutes::ENDPOINT_ICCIDS.'\/\d+/', $this->path)) {
            $body = $this->iccids();
        }

        return new FulfilledPromise(
            new Response($status, ['Content­Type' => 'application/json'], $body)
        );
    }

    private function sivServerRoutes(): array
    {
        return [
            SivRoutes::AUTH => $this->fetchFile(ClaroBRResponseBook::SUCCESS_AUTH),
            SivRoutes::AUTH_PROMOTER => $this->promoterAuth(),
            SivRoutes::UTILS => $this->fetchFile(ClaroBRResponseBook::SUCCESS_UTILS),
            SivRoutes::ENDPOINT_CREDIT_ANALYSIS => $this->fetchFile(ClaroBRResponseBook::SUCCESS_CREDIT_ANALYSIS),
            SivRoutes::REBATE => $this->rebate(),
            SivRoutes::LIST_PLANS => $this->listPlans(),
            SivRoutes::ENDPOINT_SALES => $this->endpointSales(),
            SivRoutes::ENDPOINT_ACTIVATION => $this->responseToActivation(),
            SivRoutes::ENDPOINT_AUTHENTICATE => $this->authenticate(),
            SivRoutes::ENDPOINT_SAVE_STATUS_BRSCAN => $this->saveAuthenticate(),
            SivRoutes::RESIDENTIAL_PLANS_BY_CITY => $this->plansAndPromotionsResidentials(),
            SivRoutes::CHECK_AUTOMATIC_REGISTRATION_STATUS => $this->checkAutomaticRegistrationStatus(),
        ];
    }

    private function fetchFile(string $path): Stream
    {
        return stream_for(file_get_contents($path));
    }

    private function rebate(): Stream
    {
        if (Str::is($this->query, ClaroBRTestBook::ONLY_NETWORK_QUERY_STRING)) {
            return $this->fetchFile(ClaroBRResponseBook::VALID_LIST_OF_REBATE);
        }
        if ($this->matchIn($this->query, ClaroBRTestBook::INVALID_PLAN_FOR_REBATE)) {
            return $this->fetchFile(ClaroBRResponseBook::INVALID_REBATE);
        }

        return $this->fetchFile(ClaroBRResponseBook::VALID_INDIVIDUAL_REBATE);
    }

    private function listPlans(): Stream
    {
        if ($this->matchIn($this->query, ClaroBRTestBook::SUCCESS_PLANS_DDD)) {
            return $this->fetchFile(ClaroBRResponseBook::PLANS_AREA_CODE_11);
        }

        return $this->fetchFile(ClaroBRResponseBook::CLARO_EMPTY_PLANS);
    }

    private function endpointSales(): Stream
    {
        if ($this->matchIn($this->query, ClaroBRTestBook::SUCCESS_VENDA_ID)) {
            return $this->fetchFile(ClaroBRResponseBook::CLARO_BANDA_LARGA_QUERY_SALE);
        }
        if ($this->matchIn($this->params, ClaroBRTestBook::SUCESS_CUSTOMER_BANDA_LARGA)) {
            return $this->fetchFile(ClaroBRResponseBook::CLARO_BANDA_LARGA_SAVE_SALE);
        }

        return $this->fetchFile(ClaroBRResponseBook::CLARO_CONTROLE_BOLETO_SAVE_SALE);
    }

    private function responseToActivation(): Stream
    {
        if ($this->matchIn($this->params, ClaroBRTestBook::ERROR_SERVICE_TRANSACTION)) {
            return $this->fetchFile(ClaroBRResponseBook::ERROR_ACTIVATION);
        }

        if ($this->matchIn($this->params, ClaroBRTestBook::SELECT_LIST_MSISDN)) {
            return $this->fetchFile(ClaroBRResponseBook::SELECT_MSISDN_ACTIVATION);
        }

        if ($this->matchIn($this->params, ClaroBRTestBook::SUCCESS_MSISDN)) {
            return $this->fetchFile(ClaroBRResponseBook::SUCCESS_ACTIVATION);
        }

        if ($this->matchIn($this->params, ClaroBRTestBook::SELECT_MSISDN_SERVICO_ID)) {
            return $this->fetchFile(ClaroBRResponseBook::SELECT_MSISDN_ACTIVATION);
        }

        return $this->fetchFile(ClaroBRResponseBook::SUCCESS_ACTIVATION);
    }

    private function promoterAuth(): Stream
    {
        if ($this->matchIn($this->params, ClaroBRTestBook::PROMOTER_SIX12)) {
            return $this->fetchFile(ClaroBRResponseBook::SUCCESS_AUTH_REQUEST_TOKEN);
        }

        if ($this->matchIn($this->params, ClaroBRTestBook::PROMOTER_TKNF1)) {
            return $this->fetchFile(ClaroBRResponseBook::EXCEPTION_TOKEN_NOT_FOUND);
        }

        if ($this->matchIn($this->params, ClaroBRTestBook::PROMOTER_ZALG1)) {
            return $this->fetchFile(ClaroBRResponseBook::SUCCESS_AUTH_PROMOTER_TWO_POINTS);
        }

        return $this->fetchFile(ClaroBRResponseBook::SUCCESS_AUTH_PROMOTER);
    }

    private function matchIn(string $query, string $value): bool
    {
        return (bool) preg_match('/' . $value . '/', $query);
    }

    private function authenticate(): Stream
    {
        $params = json_decode($this->params, true);
        $cpf    = data_get($params, 'customer_cpf');

        return $cpf === ClaroBRTestBook::ERROR_AUTENTICA
            ? $this->fetchFile(ClaroBRResponseBook::AUTENTICA_ERROR)
            : $this->fetchFile(ClaroBRResponseBook::AUTENTICA_SUCCESS);
    }

    private function saveAuthenticate(): Stream
    {
        $params = json_decode($this->params, true);
        $cpf    = data_get($params, 'customer_cpf');
        return  $cpf === ClaroBRTestBook::SUCCESS_CUSTOMER
                ? $this->fetchFile(ClaroBRResponseBook::AUTENTICA_SAVE_STATUS_SUCCESS)
                : $this->fetchFile(ClaroBRResponseBook::AUTENTICA_SAVE_STATUS_ERROR);
    }

    private function iccids(): Stream
    {
        preg_match('/\d{6,}/', $this->path, $prefix);
        $prefix = collect($prefix)->first() ?? '';
        if ($this->matchIn(ClaroBRTestBook::ICCID_PREFIX_WITH_SIMCARD, $prefix)) {
            return $this->fetchFile(ClaroBRResponseBook::ICCID_SIMCARDS);
        }
        return $this->fetchFile(ClaroBRResponseBook::ICCID_WITHOUT_SIMCARDS);
    }

    private function plansAndPromotionsResidentials(): Stream
    {
        $onlyNoNeedViabilityPlans = data_get(json_decode($this->params), 'only_no_need_viability_plans', 0);

        if ($onlyNoNeedViabilityPlans === 0) {
            return $this->fetchFile(ClaroBRResponseBook::PLANS_WITH_VIABILITY);
        }

        return $this->fetchFile(ClaroBRResponseBook::PLANS_WITHOUT_VIABILITY);
    }
    public function checkAutomaticRegistrationStatus(): Stream
    {
        return $this->fetchFile(ClaroBRResponseBook::SUCCESS_CHECK_AUTOMATIC_REGISTRATION_STATUS);
    }
}
