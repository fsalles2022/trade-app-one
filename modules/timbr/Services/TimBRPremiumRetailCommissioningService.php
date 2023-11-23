<?php

declare(strict_types=1);

namespace TimBR\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use TimBR\Connection\TimPremiumCommissioning\TimCommissioningConnection;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Enumerators\Operations;
use DateTimeInterface;

class TimBRPremiumRetailCommissioningService
{
    /** @var SaleService **/
    private $saleService;

    /** @var TimCommissioningConnection */
    private $commissioningConnection;

    public function __construct(SaleService $saleService, TimCommissioningConnection $commissioningConnection)
    {
        $this->saleService             = $saleService;
        $this->commissioningConnection = $commissioningConnection;
    }

    /** @return int[] */
    public function sendSalesToCommissioningByRange(Carbon $startDate, Carbon $endDate, bool $forceSend = false): array
    {
        $finished                  = false;
        $skip                      = 0;
        $take                      = (int) config('integrations.timBR.tim-premium-retail-sales-per-page', 10);
        $totalProcessedWithSuccess = 0;
        $totalProcessedWithErrors  = 0;

        while ($finished === false) {
            $sales = $this->getSales(
                $startDate,
                $endDate,
                $skip,
                $take,
                $forceSend
            );

            if ($sales->isEmpty()) {
                $finished = true;
                continue;
            }

            /** @var Sale $sale */
            foreach ($sales as $sale) {
                [$withSuccess, $withErrors] = $this->sendSaleToCommissioning($sale);

                $totalProcessedWithSuccess += $withSuccess;
                $totalProcessedWithErrors  += $withErrors;
            }

            $skip += $take;
        }

        return [
            $totalProcessedWithSuccess,
            $totalProcessedWithErrors
        ];
    }

    private function getSales(
        Carbon $startDate,
        Carbon $endDate,
        int $skip,
        int $take,
        bool $forceSend = false
    ): Collection {
        if ($forceSend) {
            return $this->saleService->filterAll(
                [
                    'startDate'     => $startDate->format('Y-m-d H:i:s'),
                    'endDate'       => $endDate->format('Y-m-d H:i:s'),
                    'status'        => [
                        ServiceStatus::APPROVED,
                        ServiceStatus::ACCEPTED,
                    ],
                    'operator'      => Operations::TIM,
                    'operations'    => Operations::TIM_PREMIUM_RETAIL_OPERATIONS,
                ],
                $skip,
                $take
            );
        }


        return $this->saleService->filterAll(
            [
                'startDate'                 => $startDate->format('Y-m-d H:i:s'),
                'endDate'                   => $endDate->format('Y-m-d H:i:s'),
                'status'                    => [
                    ServiceStatus::APPROVED,
                    ServiceStatus::ACCEPTED,
                ],
                'operator'                  => Operations::TIM,
                'operations'                => Operations::TIM_PREMIUM_RETAIL_OPERATIONS,
                'sentToTimCommissioning'    => null,
            ],
            $skip,
            $take
        );
    }

    /** @return int[] */
    public function sendSaleToCommissioning(Sale $sale): array
    {
        $totalProcessedWithSuccess = 0;
        $totalProcessedWithErrors  = 0;

        /** @var Service $service */
        foreach ($sale->services as $service) {
            if (! in_array($service->operation, Operations::TIM_PREMIUM_RETAIL_OPERATIONS)) {
                continue;
            }

            try {
                $response = $this->commissioningConnection->send(
                    $this->getPayloadBySaleAndService($sale, $service)
                );

                preg_match('/\[.*\]/', (string) $response->get(), $matches);

                $responseBody = json_decode($matches[0] ?? '{}', true);

                $error = data_get($responseBody, '0.erro', '');

                // Necessary compare $error for identify if has error
                if ($response->isSuccess() === false ||  $error !== 'sem erro, importado com sucesso.') {
                    $totalProcessedWithErrors++;

                    $this->saleService->pushLogService(
                        $service,
                        [
                            'routine' => 'TimCommissioningCommand',
                            'message' => $response->toArray(),
                            'error'   => true
                        ]
                    );

                    continue;
                }

                $totalProcessedWithSuccess++;

                $this->saleService->updateService($service, ['sentToTimCommissioning' => true]);
            } catch (\Throwable $exception) {
                $totalProcessedWithErrors++;

                $this->saleService->pushLogService(
                    $service,
                    [
                        'routine' => 'TimCommissioningCommand',
                        'message' => $exception->getMessage(),
                        'error'   => true
                    ]
                );
            }
        }

        return [
            $totalProcessedWithSuccess,
            $totalProcessedWithErrors
        ];
    }

    /** @return mixed[] */
    private function getPayloadBySaleAndService(Sale $sale, Service $service): array
    {
        $modesToSend = [
            Modes::ACTIVATION  => 'ATIVAÇÃO',
            Modes::PORTABILITY => 'ATIVAÇÃO',
            Modes::MIGRATION   => 'MIGRAÇÃO',
        ];

        $mode = $service->mode ?? null;

        $phone     = $mode === Modes::PORTABILITY ? $service->portedNumber : $service->msisdn;
        $tempPhone = $mode === Modes::PORTABILITY ? $service->msisdn : '';
        $areaCode  = empty($service->areaCode) ? substr(str_replace('+', '', $phone), 2, 2) : $service->areaCode;

        return [
            'cod_importacao'         => null,
            'dt_importado'           => now()->format(DateTimeInterface::W3C),
            'st_telefone'            => $this->getPhoneNumberFormatted((string) $phone),
            'st_telefone_temporario' => ! empty($tempPhone) ? $this->getPhoneNumberFormatted($tempPhone) : '',
            'st_imei'                => $service->imei ?? '',
            'dt_ativacao'            => $sale->createdAt->format(DateTimeInterface::W3C),
            'st_cpf_cnpj'            => $service->customer['cpf'] ?? '',
            'st_canal_vendas'        => $sale->pointOfSale['providerIdentifiers']['TIM'] ?? '',
            'st_desconto'            => $service->discount['discount'] ?? 0.00,
            'st_ddd'                 => $areaCode,
            'st_plano'               => $service->productName,
            'fidelizacao'            => $this->getLoyaltyForPayloadByService($service),
            'st_login'               => $sale->user['cpf'] ?? '',
            'num_nf'                 => null,
            'lk_nf'                  => null,
            'cod_tm'                 => $service->device['externalIdentifier'] ?? '',
            'nom_operacao'           => $modesToSend[$mode] ?? null,
            'ICCID'                  => $service->iccid,
            'st_preco'               => $service->device['priceWithout'] ?? 0.00,
            'st_preco_final'         => $service->device['priceWith'] ?? 0.00
        ];
    }

    private function getLoyaltyForPayloadByService(Service $service): string
    {
        $loyalties = data_get($service, 'loyalty.loyalties', []);

        if (empty($loyalties)) {
            return 'SEM FIDELIZAÇÃO';
        }

        if (data_get($loyalties, '0.type') === TimBRMapPlansService::LOYALTY_PRODUCT_TYPE && count($loyalties) === 1) {
            return 'FIDELIZAÇÃO DE PLANO';
        }

        if (data_get($loyalties, '0.type') === TimBRMapPlansService::LOYALTY_DEVICE_TYPE && count($loyalties) === 1) {
            return 'FIDELIZAÇÃO DE APARELHO';
        }

        return 'FIDELIZAÇÃO DE PLANO E APARELHO';
    }

    private function getPhoneNumberFormatted(string $phone): string
    {
        if (empty($phone)) {
            return '';
        }

        $phoneLength = mb_strlen(str_replace('+', '', $phone));

        if ($phoneLength <= 9) {
            return $phone;
        }

        if (in_array($phoneLength, [10, 11])) {
            return substr($phone, 2);
        }

        return substr($phone, 4);
    }
}
