<?php

namespace McAfee\Services;

use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Domain\Services\UserService;

class McAfeeInternetService
{
    private const CPF_SALE = '03533834735';
    private const SOURCE   = 'INTERNET';
    private const PDV_ID   = '1';

    protected $saleService;
    protected $userService;
    protected $saleAssistance;

    public function __construct(SaleService $saleService, UserService $userService, McAfeeSaleAssistance $saleAssistance)
    {
        $this->saleService    = $saleService;
        $this->userService    = $userService;
        $this->saleAssistance = $saleAssistance;
    }

    public function subscription(array $data): array
    {
        $user = $this->userService->findBy(self::CPF_SALE);
        Auth::setUser($user);

        $product     = data_get($data, 'service.product');
        $customer    = data_get($data, 'service.customer');
        $captcha     = data_get($data, 'captcha');

        $captchaData = json_decode(base64_decode($captcha), true);
        $captchaCode = is_array($captchaData) && isset($captchaData['code']) ? (string) $captchaData['code'] : '';
        $captchaKey  = is_array($captchaData) && isset($captchaData['key']) ? (string) $captchaData['key'] : '';

        $request = [[
            'operator' => Operations::MCAFEE,
            'operation' => Operations::MCAFEE_MULTI_ACCESS,
            'mode' => Modes::ACTIVATION,
            'product' => $product,
            'customer' => $customer
        ]];

        $sale    = $this->saleService->new($captchaCode, $captchaKey,self::SOURCE, $user, $request, self::PDV_ID);
        $service = $sale->services->first();

        return $this->saleAssistance->integrateService($service, $data);
    }
}
