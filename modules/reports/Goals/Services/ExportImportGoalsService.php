<?php

namespace Reports\Goals\Services;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Reports\Goals\Enum\GoalsTypesEnum;
use Reports\Goals\Exceptions\GoalsExceptions;
use Reports\Goals\Exportables\GoalsExport;
use Reports\Goals\Importables\GoalImportable;
use Reports\Goals\Repository\GoalRepository;
use Reports\Services\SalesByPointsOfSalesAndMonths;
use TradeAppOne\Domain\Importables\ImportEngine;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Services\HierarchyService;

class ExportImportGoalsService
{
    protected $hierarchyService;
    protected $goalRepository;

    public function __construct(GoalRepository $goalRepository, HierarchyService $hierarchyService)
    {
        $this->hierarchyService = $hierarchyService;
        $this->goalRepository   = $goalRepository;
    }

    public function import(UploadedFile $file, User $user)
    {
        $goalsTypes   = $user->getNetwork()->goalsTypes;
        $pointsOfSale = $this->hierarchyService->getPointsOfSaleThatBelongsToUser($user);

        $importable = app()->makeWith(
            GoalImportable::class,
            [
                'pointsOfSale' => $pointsOfSale,
                'goalsTypes'   => $goalsTypes
            ]
        );

        $engine = app()->makeWith(
            ImportEngine::class,
            [
                'importable' => $importable
            ]
        );

        return $engine->process($file);
    }

    public function export(User $user, array $exportForm)
    {
        $filters = $this->getExportationFilters($user, $exportForm);
        $goals   = $this->goalRepository->getGoalsBasedPointsOfSaleAndMonths($filters, array_wrap(GoalsTypesEnum::TOTAL));

        if ($goals->isEmpty()) {
            throw GoalsExceptions::monthGoalsNotFound();
        }

        $filters['cnpjs'] = $goals->pluck('pointOfSale.cnpj')->toArray();

        $salesByPointsOfSalesAndMonths = resolve(SalesByPointsOfSalesAndMonths::class);
        $goalsAccomplished             = $salesByPointsOfSalesAndMonths->getSales($filters);

        return (new GoalsExport())->exportToCsv($goals, $goalsAccomplished);
    }

    private function getExportationFilters($user, array $filters)
    {
        $userSalePoints = $this->hierarchyService->getPointsOfSaleThatBelongsToUser($user);
        $userNetworks   = $this->hierarchyService->getNetworksThatBelongsToUser($user);

        $pointsOfSale = data_get($filters, 'pointsOfSale', null);
        $networks     = data_get($filters, 'networks', null);

        if ($pointsOfSale === null) {
            $pointsOfSale = $userSalePoints
                ->pluck('cnpj')
                ->toArray();
        } else {
            $pointsOfSale = $userSalePoints
                ->whereIn('cnpj', $pointsOfSale)
                ->pluck('cnpj')
                ->toArray();
        }

        if ($networks === null) {
            $networks = $userNetworks->pluck('id')->toArray();
        } else {
            $networks = $userNetworks
                ->whereIn('slug', $networks)
                ->pluck('id')
                ->toArray();
        }

        $months = data_get($filters, 'months', now()->month);
        $year   = (Carbon::now()->year);

        return [
            'cnpjs'    => array_wrap($pointsOfSale),
            'months'   => array_wrap($months),
            'networks' => array_wrap($networks),
            'year'     => $year
        ];
    }
}
