<?php

declare(strict_types=1);

namespace TimBR\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use TimBR\Connection\BrScan\BrScanConnection;
use TimBR\Enumerators\TimBRInvoiceTypes;
use TimBR\Enumerators\TimBrScanAuthenticateStatus;
use TimBR\Enumerators\TimBrScanSaleTermStatus;
use TimBR\Enumerators\TimBRSegments;
use TimBR\Exceptions\BrScanGenerateLinkException;
use TimBR\Exceptions\BrScanGenerateSaleTermException;
use TimBR\Exceptions\BrScanSaleTermStatusException;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\PointOfSaleRepository;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Domain\HttpClients\Responseable;

class BrScanService
{
    /** @var BrScanConnection */
    private $brScanConnection;

    /** @var PointOfSaleRepository */
    private $pointOfSaleRepository;

    /** @var SaleRepository */
    private $saleRepository;

    public function __construct(
        BrScanConnection $brScanConnection,
        PointOfSaleRepository $pointOfSaleRepository,
        SaleRepository $saleRepository
    ) {
        $this->brScanConnection      = $brScanConnection;
        $this->pointOfSaleRepository = $pointOfSaleRepository;
        $this->saleRepository        = $saleRepository;
    }

    /** @throws BrScanGenerateLinkException */
    public function generateAuthenticateLink(string $cpf, string $email, string $phone, int $pointOfSaleId): array
    {
        $pointOfSale = $this->pointOfSaleRepository->find($pointOfSaleId);

        $response = $this->brScanConnection->generateAuthenticateLink($pointOfSale->providerIdentifiers['TIM'] ?? '', $cpf, $email, $phone);

        if ($response->isSuccess() === false) {
            throw new BrScanGenerateLinkException();
        }

        return $response->toArray();
    }

    public function getAuthenticateStatus(int $authenticateId): array
    {
        $response = $this->brScanConnection->getAuthenticateStatus($authenticateId);

        $status = TimBrScanAuthenticateStatus::getTransformedStatus((string) $response->get('statusBiometria', ''));

        if ($status === null) {
            $status = TimBrScanAuthenticateStatus::getTransformedStatus((string) $response->get('resultadoAnalise', ''));
        }

        return [
            'authenticate' => $response->toArray(),
            'status'       => $status,
        ];
    }

    /** @throws BrScanGenerateSaleTermException */
    public function generateSaleTermForSignature(string $serviceTransaction): array
    {
        $service     = $this->saleRepository->findInSale($serviceTransaction);
        $pointOfSale = $service->sale->pointOfSale;
        $seller      = $service->sale->user;

        $authenticateId = (int) data_get($service, 'authenticate.linkId');

        $firstName           = $service->customer['firstName'] ?? '';
        $lastName            = $service->customer['lastName'] ?? '';
        $rgDate              = $service->customer['rgDate'] ?? '';
        $birthday            = $service->customer['birthday'] ?? '';
        $rgLocal             = $service->customer['rgLocal'] ?? '';
        $rgState             = $service->customer['rgState'] ?? '';
        $operatorIdentifiers = $service->operatorIdentifiers ?? '';
        $customerPhoneNumber = substr(str_replace('+', '', $service->customer['mainPhone'] ?? ''), 2);
        $msisdn              = substr(str_replace('+', '', $service->msisdn ?? ''), 2);

        $term = [
            'tipoPessoa' => 'PF',
            'origem' => 'Varejo Premium',
            'nome' => "{$firstName} {$lastName}",
            'cpf' => $service->customer['cpf'] ?? '',
            'rg' => $service->customer['rg'] ?? '',
            'email' => $service->customer['email'] ?? '',
            'telefone' => $customerPhoneNumber,
            'orgaoExpedidor' => "{$rgLocal}-{$rgState}",
            'dataEmissao' => ! empty($rgDate) ? Carbon::make($rgDate)->format('d/m/Y') : '',
            'dataNascimento' => ! empty($birthday) ? Carbon::make($birthday)->format('d/m/Y') : '',
            'sexo' => $service->customer['gender'] ?? '',
            'nacionalidade' => "Brasileiro",
            'nomeMae' => $service->customer['filiation'] ?? '',
            'endereco' => $service->customer['local'] ?? '',
            'numero' => $service->customer['number'] ?? '',
            'complemento' => $service->customer['complement'] ?? '',
            'bairro' => $service->customer['neighborhood'] ?? '',
            'cidade' => $service->customer['city'] ?? '',
            'uf' => $service->customer['state'] ?? '',
            'cep' => $service->customer['zipCode'] ?? '',
            'produto' => $this->getProductTermByService($service),
            'fidelizacao' => empty(data_get($service, 'loyalty.id')) ? 'Nao' : 'Sim',
            'plano' => $this->getPlanLoyaltyFieldToSaleTermByService($service) ? 'Sim' : 'Nao',
            'aparelho' => $this->getDeviceLoyaltyFieldToSaleTermByService($service) ? 'Sim' : 'Nao',
            'planoAparelho' => $this->getPlanDeviceLoyaltyFieldToSaleTermByService($service) ? 'Sim' : 'Nao',
            'servico' => $this->getServiceFieldToSaleTermByService($service),
            'protocolo' => data_get($operatorIdentifiers, 'protocol', '   '), // These spaces are necessary to work in BRScan (ByPass validation)
            'timChip' => $service->iccid ?? '00000000000000000000', // Requested by TIM this default value
            'numeroTelefone' => $msisdn,
            'oferta' => $service->productName ?? '',
            'valorMensal' => (string) ($service->price ?? 0),
            'vencimento' => $service->dueDate ?? '',
            'formaPagamento' =>$this->getInvoiceTypeForSaleTermByService($service),
            'bandeiraCartao' => $service->creditCard['brand'] ?? '',
            'numeroCartao' => $this->getLastFourDigits($service),
            'numeroBanco' => $service->directDebit['bankId']['id'] ?? '',
            'nomeBanco' => $service->directDebit['bankId']['label'] ?? '',
            'contaCorrente' => $service->directDebit['checkingAccount'] ?? '',
            'agencia' => $service->directDebit['agency'] ?? '',
            'tipoFatura' => $service->billType ?? '',
            'numPortabilidade' => $service->portedNumber ?? '',
            'prazoPortabilidade' => now()->addWeekday(3)->format('d/m/y'),
            'protocoloPortabilidade' => ! empty($service->portedNumber) ? data_get($operatorIdentifiers, 'protocol', '   ') : '   ', // These spaces are necessary to work in BRScan (ByPass validation)
            'aceitePromocoes' => "Nao",
            'aceiteFaturaDigital' => $service->billType === 'Conta Online' ? 'Sim' : 'Nao',
            'aceiteFoto' => "Sim",
            'aceiteCobertura' => "Sim",
            'nomeLoja' => $pointOfSale['label'] ?? '',
            'custCode' => $pointOfSale['providerIdentifiers']['TIM'] ?? '',
            'localLoja' => $pointOfSale['city'] ?? '',
            'nomeVendedor' => ($seller['firstName'] ?? '') . ' ' . ($seller['lastName'] ?? ''),
            'cpfVendedor' => $seller['cpf'],
            'clienteCessionario' => '',
            'clienteCedente' => '',
            'nomeTestemunhaUm' => $service->customer['witnessName1'] ?? '',
            'rgTestemunhaUm' => $service->customer['witnessRg1'] ?? '',
            'nomeTestemunhaDois' => $service->customer['witnessName2'] ?? '',
            'rgTestemunhaDois' => $service->customer['witnessRg2'] ?? '',
            'tipoVenda' => $service->operation === Operations::TIM_BLACK_MULTI_DEPENDENT ? 'Dependente' : 'Venda',
            'numeroTelefoneTitularDependente' => $service->masterNumber ?? '',
            'ofertaDependente' => $service->operation === Operations::TIM_BLACK_MULTI_DEPENDENT ? $service->productName ?? '' : '',
            'protocoloAtendimentoDependente' => $service->operation === Operations::TIM_BLACK_MULTI_DEPENDENT ? data_get($operatorIdentifiers, 'protocol', '') : '',
            'numeroTelefoneDependente' => $service->operation === Operations::TIM_BLACK_MULTI_DEPENDENT ? $msisdn : '',
            'verificarAreaCobertura' => 'Não',
            'clienteLoja' => "{$firstName} {$lastName}",
            'dataLoja' => Carbon::parse($service->sale->createdAt)->format('d/m/Y'),
        ];

        $response = $this->brScanConnection->generateSaleTermForSignature($authenticateId, $term);

        if ($response->isSuccess() === false) {
            $message = $response->get('message');

            throw new BrScanGenerateSaleTermException(! empty($message) ? $message : $response->get('mensagem', ''));
        }

        return $response->toArray();
    }

    private function getServiceFieldToSaleTermByService(Service $service): string
    {
        $mode    = trans('timBR::messages.brScan.sale_term.' . $service->mode);
        $service = $service->operation === Operations::TIM_BLACK_MULTI_DEPENDENT ? trans('timBR::messages.brScan.sale_term.dependent') : trans('timBR::messages.brScan.sale_term.' . TimBRSegments::TRANSLATE[$service->operation]);

        return "{$mode} {$service}";
    }

    private function getPlanLoyaltyFieldToSaleTermByService(Service $service): bool
    {
        $loyalties = collect(data_get($service, 'loyalty.loyalties', []));

        return $this->hasPlanLoyaltyByLoyalties($loyalties);
    }

    private function getDeviceLoyaltyFieldToSaleTermByService(Service $service): bool
    {
        $loyalties = collect(data_get($service, 'loyalty.loyalties', []));

        return $this->hasDeviceLoyaltyByLoyalties($loyalties);
    }
    private function getPlanDeviceLoyaltyFieldToSaleTermByService(Service $service): bool
    {
        $loyalties = collect(data_get($service, 'loyalty.loyalties', []));

        return $loyalties->count() === 2 && $this->hasPlanLoyaltyByLoyalties($loyalties) && $this->hasDeviceLoyaltyByLoyalties($loyalties);
    }

    private function hasDeviceLoyaltyByLoyalties(Collection $loyalties): bool
    {
        return $loyalties->where('type', '=', 'Aparelho')->isNotEmpty();
    }

    private function hasPlanLoyaltyByLoyalties(Collection $loyalties): bool
    {
        return $loyalties->where('type', '=', 'Produto')->isNotEmpty();
    }

    private function getInvoiceTypeForSaleTermByService(Service $service): string
    {
        if ($service->invoiceType === TimBRInvoiceTypes::DEBITO_AUTOMATICO) {
            return 'Débito Automático';
        }

        return (string) ($service->invoiceType ?? '');
    }

    private function getProductTermByService(Service $service): string
    {
        $posPagoOperations = [
            Operations::TIM_BLACK,
            Operations::TIM_BLACK_MULTI,
            Operations::TIM_BLACK_EXPRESS,
            Operations::TIM_BLACK_MULTI_DEPENDENT,
        ];

        if (in_array($service->operation, $posPagoOperations)) {
            return 'PÓS-PAGO';
        }

        return 'Controle';
    }

    /** @throws BrScanSaleTermStatusException */
    public function getSaleTermStatus(int $authenticateId): array
    {
        $response = $this->brScanConnection->getSaleTermStatus($authenticateId);

        if ($response->isSuccess() === false) {
            $message = $response->get('message');

            throw new BrScanSaleTermStatusException(! $message ? $response->get('mensagem') : '');
        }

        return [
            'saleTerm' => $response->toArray(),
            'status'   => TimBrScanSaleTermStatus::getTransformedStatus((string) $response->get('resultadoAnalise', '')),
        ];
    }

    public function sendWelcomeKit(int $authenticateId): Responseable
    {
        return $this->brScanConnection->sendWelcomeKit($authenticateId);
    }
    
    public function getLastFourDigits(Service $service): string
    {
        $cardNumber = (string) $service->creditCard['pan'] ?? '';
        $cardNumber = trim($cardNumber);

        if (empty($cardNumber)) {
            return '';
        };

        return mb_substr($cardNumber, -4);
    }
}
