<?php

namespace Reports\SubModules\Hourly;

use Illuminate\Support\Collection;
use Reports\Goals\Enum\GoalsTypesEnum;
use Reports\Goals\Services\GoalService;
use Reports\Services\SalesByMonthAndPeriod;
use Reports\SubModules\Hourly\Helpers\ConsolidateOperatorMapper;
use Reports\SubModules\Hourly\Helpers\CriteriaHourlyDminus;
use Reports\SubModules\Hourly\Helpers\HeaderMapper;
use Reports\SubModules\Hourly\Helpers\ScaffoldToGroupOfOperators;
use TradeAppOne\Domain\Models\Tables\Hierarchy;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Services\HierarchyService;
use TradeAppOne\Domain\Services\NetworkService;

class HourlyReportService
{
    protected $hierarchyService;
    protected $monthReportService;
    protected $reportDayService;
    protected $networkService;
    protected $goalService;

    private $strategyCriteria;
    private $monthConsolidateFromNetwork;
    private $network;
    private $goalsTypes;

    public function __construct(
        HierarchyService $hierarchyService,
        NetworkService $networkService,
        GoalService $goalService,
        SalesByMonthAndPeriod $monthReportService
    ) {
        $this->hierarchyService   = $hierarchyService;
        $this->networkService     = $networkService;
        $this->goalService        = $goalService;
        $this->monthReportService = $monthReportService;
    }

    public function get(array $options)
    {
        $this->goalsTypes       = data_get($options, 'goalType', GoalsTypesEnum::TOTAL);
        $this->strategyCriteria = $this->defineDminusCriteriaStrategy($options);

        $this->network                     = $this->networkService->findOneBySlug($options['network']);
        $hierarchiesOfNetwork              = $this->hierarchyService->getHierarchiesOfNetwork($this->network);
        $this->monthConsolidateFromNetwork = $this->getConsolidateMonthsByPointsOfSale($this->network->pointsOfSale);

        $headers = HeaderMapper::map(
            $this->network->availableServicesRelation,
            $this->monthConsolidateFromNetwork['data'],
            $this->strategyCriteria
        );

        $body = $this->buildBody($hierarchiesOfNetwork);

        $report = [
            'DATE'    => $this->strategyCriteria->day->format('d/m/Y'),
            'TIME'    => $this->strategyCriteria->day->toTimeString(),
            'NETWORK' => $this->network->label,
            'HEADERS' => $headers,
            'BODY'    => $body
        ];
        return $report;
    }

    private function defineDminusCriteriaStrategy(array $options): CriteriaHourlyDminus
    {
        if (data_get($options, 'date') === null) {
            $options['date'] = now();
        }

        $strategyCriteria = new CriteriaHourlyDminus($options['date']);

        return $strategyCriteria;
    }

    private function getConsolidateMonthsByPointsOfSale(Collection $pointsOfSaleOfGroup): array
    {
        $listOfCnpjs = ['pointsOfSale' => $pointsOfSaleOfGroup->pluck('cnpj')->toArray()];

        return $this->monthReportService->getResume($listOfCnpjs, $this->strategyCriteria);
    }

    private function buildBody(Collection $hierarchiesOfNetwork)
    {
        $body = [];

        foreach ($hierarchiesOfNetwork as $hierarchy) {
            $pointsOfSaleOfHierarchy = $hierarchy->pointsOfSale;

            if ($pointsOfSaleOfHierarchy->isEmpty()) {
                $hierarchyBlock = $this->calculateHierarchyOfHierarchies($hierarchy);
            } else {
                $hierarchyBlock = $this->calculateHierarchyOfPointsOfSale($pointsOfSaleOfHierarchy, $hierarchy);
            }

            $body += $hierarchyBlock;
        }
        return $body;
    }

    private function calculateHierarchyOfHierarchies(Hierarchy $baseHierarchy)
    {
        $hierarchiesChildren            = $this->hierarchyService->getChildrenHierarchiesOfHierarchy($baseHierarchy);
        $pointsOfSaleOfGroup            = $this->hierarchyService->getPointsOfSaleOfHierarchyTopToBottom($baseHierarchy);
        $baseHierarchyConsolidateResult = $this->getConsolidateMonthsByPointsOfSale($pointsOfSaleOfGroup);
        $baseHierarchyConsolidate       = self::mapPosAndPrePagoGroups($baseHierarchyConsolidateResult['data']);

        $mappedHierarchiesChild = [];

        foreach ($hierarchiesChildren as $hierarchyChild) {
            $childPointsOfSale = $this->hierarchyService->getPointsOfSaleOfHierarchyTopToBottom($hierarchyChild);
            $elasticResult     = $this->getConsolidateMonthsByPointsOfSale($childPointsOfSale);

            $hierarchyBody          = self::mapPosAndPrePagoGroups($elasticResult['data']);
            $totalDone              = data_get($baseHierarchyConsolidate, 'RESUME.TOTAL');
            $hierarchyBody['GOALS'] = $this->calculateGoalsFromHierarchy($childPointsOfSale, $totalDone);

            $mappedHierarchiesChild[$hierarchyChild->label] = $hierarchyBody;
        }

        $body[$baseHierarchy->label]['RESUME']  = $baseHierarchyConsolidate;
        $body[$baseHierarchy->label]['DETAILS'] = $mappedHierarchiesChild;
        return $body;
    }

    private function mapPosAndPrePagoGroups(array $elasticResult): array
    {
        $posPagoScaffold = ScaffoldToGroupOfOperators::createPosPagoWithCustomOperation(
            $this->network->availableServicesRelation,
            0
        );

        $posPagoOperators = ConsolidateOperatorMapper::mapMonthByPosPago($posPagoScaffold, $elasticResult);
        $posPagoDay       = ConsolidateOperatorMapper::mapDayByPosPago($posPagoScaffold, $elasticResult);
        $posPagoDminus    = ConsolidateOperatorMapper::mapDminusByPosPago($posPagoScaffold, $elasticResult);
        $prePagoScaffold  = ScaffoldToGroupOfOperators::createPrePagoWithCustomOperation(
            $this->network->availableServicesRelation,
            0
        );

        $prePagoOperators = ConsolidateOperatorMapper::mapMonthByPrePago($prePagoScaffold, $elasticResult);
        $prePagoDay       = ConsolidateOperatorMapper::mapDayByPrePago($prePagoScaffold, $elasticResult);
        $prePagoDminus    = ConsolidateOperatorMapper::mapDminusByPrePago($prePagoScaffold, $elasticResult);

        $consolidateMonthValues = ConsolidateOperatorMapper::mapValuesFromConsolidateOperations($elasticResult);

        $posPagoOperatorsTotal = array_sum($posPagoOperators);
        $prePagoOperatorsTotal = array_sum($prePagoOperators);
        $totalOperatorsMonth   = $posPagoOperatorsTotal + $prePagoOperatorsTotal;

        $structure = [
            'RESUME'   => [
                'TOTAL'  => $totalOperatorsMonth,
                'VALUES' => $consolidateMonthValues
            ],
            'POS_PAGO' => array_merge(
                $posPagoOperators,
                [
                    'DAY'                                        => $posPagoDay,
                    'DMINUS' . $this->strategyCriteria->strategy => $posPagoDminus,
                    'TOTAL'                                      => $posPagoOperatorsTotal,
                    'DMINUS'                                     => 0 //Tests Dependent
                ]
            ),
            'PRE_PAGO' => array_merge(
                $prePagoOperators,
                [
                    'DAY'                                        => $prePagoDay,
                    'DMINUS' . $this->strategyCriteria->strategy => $prePagoDminus,
                    'TOTAL'                                      => $prePagoOperatorsTotal,
                    'DMINUS'                                     => 0 //Tests Dependent
                ]
            )
        ];
        return $structure;
    }

    public function calculateGoalsFromHierarchy(Collection $pointsOfSale, $totalDone = 0)
    {
        $goal = $this->goalService->findByCollectionPointOfSale(
            $pointsOfSale,
            $this->strategyCriteria->day,
            $this->goalsTypes
        );

        if ($goal->isNotEmpty()) {
            $totalGoal        = $goal->sum('goal');
            $goals['TOTAL']   = $totalGoal;
            $goals['PERCENT'] = round(($totalDone / $totalGoal) * 100, 2);

            $gap          = $totalGoal - $totalDone;
            $goals['GAP'] = $gap < 0 ? 0 : $gap;

            return $goals;
        }

        return ['TOTAL' => '-', 'PERCENT' => '-', 'GAP' => '-'];
    }

    private function calculateHierarchyOfPointsOfSale($pointsOfSaleOfHierarchy, $hierarchy)
    {
        $pointsOfSaleDetails   = $this->getConsolidateMonthsByPointsOfSale($pointsOfSaleOfHierarchy);
        $pointsOfSaleOfElastic = collect($this->monthConsolidateFromNetwork['data'][SalesByMonthAndPeriod::POINT_OF_SALE_DETAIL]['buckets']);

        $mappedPointsOfSales = [];

        foreach ($pointsOfSaleOfHierarchy as $pointOfSale) {
            $pointFromElastic = $pointsOfSaleOfElastic->where('key', $pointOfSale->cnpj)->first();

            $bodyScaffoldP = [];

            if ($pointFromElastic) {
                $bodyScaffoldP = self::mapPosAndPrePagoGroups($pointFromElastic);
            }

            $bodyScaffoldP['GOALS'] = $this->calculateGoals($pointOfSale, data_get($bodyScaffoldP, 'RESUME.TOTAL'));

            $mappedPointsOfSales[$pointOfSale->label] = $bodyScaffoldP;
        }

        $resume          = self::mapPosAndPrePagoGroups($pointsOfSaleDetails['data']);
        $resume['GOALS'] = $this->calculateGoalsFromHierarchy($pointsOfSaleOfHierarchy, $resume['RESUME']['TOTAL']);

        $body[$hierarchy->label]['RESUME']  = $resume;
        $body[$hierarchy->label]['DETAILS'] = $mappedPointsOfSales;

        return $body;
    }

    private function calculateGoals(PointOfSale $pointOfSale, $done)
    {
        $goal = $this->goalService->findByPointOfSale($pointOfSale, $this->strategyCriteria->day, $this->goalsTypes);

        if ($goal) {
            $goals['TOTAL']   = $goal->goal;
            $goals['PERCENT'] = round(($done / $goal->goal) * 100, 2);

            $gap          = $goal->goal - $done;
            $goals['GAP'] = $gap < 0 ? 0 : $gap;

            return $goals;
        }

        return ['TOTAL' => '-', 'PERCENT' => '-', 'GAP' => '-'];
    }
}
