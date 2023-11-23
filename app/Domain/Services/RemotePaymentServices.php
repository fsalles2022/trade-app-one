<?php

declare(strict_types=1);

namespace TradeAppOne\Domain\Services;

use Illuminate\Support\Carbon;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Exceptions\RemotePaymentException;

class RemotePaymentServices
{
    private $saleRepository;

    public function __construct(SaleRepository $saleRepository)
    {
        $this->saleRepository = $saleRepository;
    }

    /** @throws \Throwable */
    public function findServicesByToken(string $token): ?Service
    {
        $year                    = Carbon::now()->year;
        $serviceTransaction      = base64_decode($token);
        $validServiceTransaction = (bool) preg_match("/^{$year}.*$/", $serviceTransaction);

        throw_unless($validServiceTransaction, RemotePaymentException::invalidServicePaymentToken());

        return $this->saleRepository->findInSale($serviceTransaction);
    }
}
