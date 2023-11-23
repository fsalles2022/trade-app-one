<?php

declare(strict_types=1);

namespace TimBR\Connection\BrScan;

use TimBR\Connection\TimBR;
use TradeAppOne\Domain\HttpClients\Responseable;

class BrScanConnection
{
    /** @var BrScanHttpClient */
    private $brScanHttpClient;

    public function __construct(BrScanHttpClient $brScanHttpClient)
    {
        $this->brScanHttpClient = $brScanHttpClient;
    }

    public function generateAuthenticateLink(string $custCode, string $cpf, ?string $email = null, ?string $phone = null): Responseable
    {
        $token = $this->mountBasicToken(
            TimBR::getBrScanGenerateAuthenticateApiUser(),
            TimBR::getBrScanGenerateAuthenticateApiPassword()
        );

        $body = [
            'custcode'  => $custCode,
            'cpf'       => $cpf,
            'email'     => $email,
            'telefone'  => $phone,
        ];

        return $this->brScanHttpClient->post(BrScanRoutes::GENERATE_AUTHENTICATE_LINK, $body, ['Authorization' => $token]);
    }

    public function getAuthenticateStatus(int $authenticateId): Responseable
    {
        $token = $this->mountBasicToken(
            TimBR::getBrScanAuthenticateStatusApiUser(),
            TimBR::getBrScanAuthenticateStatusApiPassword()
        );

        $body = [
            'linkId' => $authenticateId,
        ];

        return $this->brScanHttpClient->post(BrScanRoutes::AUTHENTICATE_STATUS, $body, ['Authorization' => $token]);
    }

    public function generateSaleTermForSignature(int $authenticateId, array $termPayload): Responseable
    {
        $token = $this->mountBasicToken(
            TimBR::getBrScanGenerateSaleTermForSignatureApiUser(),
            TimBR::getBrScanGenerateSaleTermForSignatureApiPassword()
        );

        $body = array_merge(
            $termPayload,
            [
                'linkId'  => $authenticateId,
            ]
        );

        return $this->brScanHttpClient->post(BrScanRoutes::SEND_SALE_FOR_TERM_SIGNATURE, $body, ['Authorization' => $token]);
    }

    public function getSaleTermStatus(int $authenticateId): Responseable
    {
        $token = $this->mountBasicToken(
            TimBR::getBrScanSaleTermStatusApiUser(),
            TimBR::getBrScanSaleTermStatusApiPassword()
        );

        $body = [
            'linkId' => $authenticateId,
        ];

        return $this->brScanHttpClient->post(BrScanRoutes::TERM_SIGNATURE_STATUS, $body, ['Authorization' => $token]);
    }

    public function sendWelcomeKit(int $authenticateId): Responseable
    {
        $token = $this->mountBasicToken(
            TimBR::getBrScanWelcomeKitApiUser(),
            TimBR::getBrScanWelcomeKitApiPassword()
        );

        $body = [
            'linkId' => $authenticateId,
        ];

        return $this->brScanHttpClient->post(BrScanRoutes::SEND_WELCOME_KIT_FOR_CUSTOMER, $body, ['Authorization' => $token]);
    }

    private function mountBasicToken(string $user, string $password): string
    {
        return "Basic ". base64_encode("{$user}:{$password}");
    }
}
