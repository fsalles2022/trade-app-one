<?php

declare(strict_types=1);

namespace Reports\SubModules\Riachuelo\Hourly\Services;

use Illuminate\Database\Eloquent\Collection;
use Reports\SubModules\Core\Models\Hierarchies;
use Reports\SubModules\Core\Models\Operations;
use Reports\SubModules\Core\Models\Operators;
use Reports\SubModules\Core\Models\Sales;
use Reports\SubModules\Core\Models\Status;
use Reports\SubModules\Hourly\Constants\ReportTypeConstants;
use Reports\SubModules\Riachuelo\Hourly\Layout\HourlyReportPdfLayout;
use Reports\SubModules\Riachuelo\Hourly\Models\HierarchySaleAccumulatorCollection;
use Reports\SubModules\Riachuelo\Hourly\Models\PointOfSaleSaleAccumulatorCollection;
use Reports\SubModules\Riachuelo\Hourly\Models\PointOfSaleSaleAccumulatorCollectionList;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Enumerators\Operations as EnumeratorsOperations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Services\HierarchyService;
use TradeAppOne\Domain\Services\NetworkService;
use TradeAppOne\Domain\Services\PointOfSaleService;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Exceptions\BusinessExceptions\NetworkNotFoundException;
use TradeAppOne\Domain\Components\Telegram\Telegram;

class HourlyReportService
{
    /** @var NetworkService */
    protected $networkService;

    /** @var HierarchyService */
    protected $hierarchyService;

    /** @var PointOfSaleService */
    protected $pointOfSaleService;

    /** @var SaleService */
    protected $saleService;

    /** @var Telegram */
    protected $telegramService;

    public function __construct(
        NetworkService $networkService,
        HierarchyService $hierarchyService,
        PointOfSaleService $pointOfSaleService,
        SaleService $saleService,
        Telegram $telegramService
    ) {
        $this->networkService     = $networkService;
        $this->hierarchyService   = $hierarchyService;
        $this->pointOfSaleService = $pointOfSaleService;
        $this->saleService        = $saleService;
        $this->telegramService    = $telegramService;
    }

    public function report(string $reportType = ReportTypeConstants::MONTH): string
    {
        $startDate = $this->defineStartDateByReportType($reportType);
        $endDate   = (new \DateTime())->modify(now()->format('H') . ":00:00");

        $operators = $this->getOperators();

        $hierarchies = $this->getHierarchiesByRiachueloNetwork();
        $sales       = $this->getSalesByHierarchiesAndOperators(
            $hierarchies,
            $operators,
            $this->getOperationsWithApprovedOrAccepted(),
            $this->getOperationsWithApproved(),
            $startDate,
            $endDate
        );

        $reportBuilder = new HourlyReportBuilder(
            $sales,
            $hierarchies,
            $operators
        );

        $reportBuilder->build();

        $filePath = $this->generateReportDocument(
            $startDate,
            $endDate,
            $reportBuilder
        );

        $this->sendDocumentToTelegram($filePath);

        return $filePath;
    }

    /** @throws NetworkNotFoundException */
    private function getHierarchiesByRiachueloNetwork(): Hierarchies
    {
        $hierarchies = $this->hierarchyService->getHierarchiesOfNetwork(
            $this->getRiachueloNetwork()
        );

        return new Hierarchies($hierarchies->all());
    }

    /** @throws NetworkNotFoundException */
    private function getRiachueloNetwork(): Network
    {
        return $this->networkService->findOneBySlug(NetworkEnum::RIACHUELO);
    }

    private function getSalesByHierarchiesAndOperators(
        Hierarchies $hierarchies,
        Operators $operators,
        Operations $operationsWithApprovedOrAccepted,
        Operations $operationsWithApproved,
        \DateTime $startDate,
        \DateTime $endDate
    ): Sales {
        $salesWithApprovedOrAccepted = $this->getSalesByFilters(
            $hierarchies,
            $operators,
            $operationsWithApprovedOrAccepted,
            new Status([
                ServiceStatus::APPROVED,
                ServiceStatus::ACCEPTED,
            ]),
            $startDate,
            $endDate
        );

        $salesWithApproved = $this->getSalesByFilters(
            $hierarchies,
            $operators,
            $operationsWithApproved,
            new Status([
                ServiceStatus::APPROVED
            ]),
            $startDate,
            $endDate
        );

        return new Sales(
            array_merge(
                $salesWithApprovedOrAccepted->all(),
                $salesWithApproved->all()
            )
        );
    }

    private function getSalesByFilters(
        Hierarchies $hierarchies,
        Operators $operators,
        Operations $operations,
        Status $status,
        \DateTime $startDate,
        \DateTime $endDate
    ): Collection {
        $hierarchiesSlugs = $hierarchies->getAllSlugs();

        return $this->saleService->filterAll([
            'startDate'     => $startDate->format('Y-m-d H:i:s'),
            'endDate'       => $endDate->format('Y-m-d H:i:s'),
            'hierarchies'   => $hierarchiesSlugs,
            'operators'     => $operators->all(),
            'operations'    => $operations->all(),
            'status'        => $status->all(),
        ]);
    }

    private function getOperators(): Operators
    {
        return new Operators([
            EnumeratorsOperations::CLARO,
            EnumeratorsOperations::TIM,
            EnumeratorsOperations::MCAFEE,
        ]);
    }

    private function getOperationsWithApprovedOrAccepted(): Operations
    {
        return new Operations([
            EnumeratorsOperations::CLARO_CONTROLE_BOLETO,
            EnumeratorsOperations::CLARO_CONTROLE_FACIL,
            EnumeratorsOperations::CLARO_POS,
            EnumeratorsOperations::TIM_CONTROLE_FATURA,
            EnumeratorsOperations::TIM_EXPRESS,
        ]);
    }

    private function getOperationsWithApproved(): Operations
    {
        return new Operations([
            EnumeratorsOperations::CLARO_BANDA_LARGA,
            EnumeratorsOperations::CLARO_FIXO,
            EnumeratorsOperations::CLARO_TELEVISAO,
            EnumeratorsOperations::CLARO_PONTO_ADICIONAL,
            EnumeratorsOperations::MCAFEE_MOBILE_SECURITY,
            EnumeratorsOperations::MCAFEE_MULTI_ACCESS,
            EnumeratorsOperations::MCAFEE_MULTI_ACCESS_TRIAL,
        ]);
    }

    private function generateReportDocument(
        \DateTime $startDate,
        \DateTime $endDate,
        HourlyReportBuilder $builder
    ): string {
        $reportPdfLayout = new HourlyReportPdfLayout(
            $startDate,
            $endDate,
            $builder->getOperators(),
            $this->mountPointOfSaleAccumulatorCollectionList($builder),
            new HierarchySaleAccumulatorCollection($builder->getHierarchiesSaleAccumulators())
        );

        return $reportPdfLayout->generate(
            storage_path('riachuelo-hourly-report.pdf')
        );
    }

    /** This method across Hierarchy with PointsOfSale */
    private function mountPointOfSaleAccumulatorCollectionList(HourlyReportBuilder $builder): PointOfSaleSaleAccumulatorCollectionList
    {
        $pointsOfSaleAccumulator            = [];
        $pointsOfSaleAccumulatorCollections = [];

        foreach ($builder->getPointsOfSaleSaleAccumulators() as $pointOfSaleSaleAccumulator) {
            $hierarchyId = data_get($pointOfSaleSaleAccumulator->getPointOfSale(), 'hierarchyId');

            if (isset($pointsOfSaleAccumulator[$hierarchyId]) === false) {
                $pointsOfSaleAccumulator[$hierarchyId] = [];
            }

            $pointsOfSaleAccumulator[$hierarchyId][] = $pointOfSaleSaleAccumulator;
        }

        foreach ($builder->getHierarchiesSaleAccumulators() as $hierarchySaleAccumulator) {
            $hierarchyId = data_get($hierarchySaleAccumulator->getHierarchy(), 'id');

            if (isset($pointsOfSaleAccumulator[$hierarchyId]) === false) {
                continue;
            }

            $pointsOfSaleAccumulatorCollections[] = new PointOfSaleSaleAccumulatorCollection(
                $hierarchySaleAccumulator->getHierarchy(),
                $pointsOfSaleAccumulator[$hierarchyId]
            );
        }

        return new PointOfSaleSaleAccumulatorCollectionList($pointsOfSaleAccumulatorCollections);
    }

    private function sendDocumentToTelegram(string $filePath): void
    {
        $chatId = config('telegram.' . NetworkEnum::RIACHUELO);

        $params = [
            'chat_id' => $chatId,
            'document' => $filePath,
            'caption' => 'Relatório Hora-Hora RIACHUELO',
        ];

        $this->telegramService->sendDocument($params);
    }

    private function defineStartDateByReportType(string $reportType): \DateTime
    {
        if ($reportType === ReportTypeConstants::DAILY) {
            return (new \DateTime())->modify('today');
        }

        return (new \DateTime())->modify('first day of this month today');
    }
}
