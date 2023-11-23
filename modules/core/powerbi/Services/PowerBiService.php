<?php

declare(strict_types=1);

namespace Core\PowerBi\Services;

use Core\PowerBi\Constants\PowerBiDashboards;
use TradeAppOne\Domain\Enumerators\Permissions;
use TradeAppOne\Domain\Enumerators\Permissions\DashboardLadsPermission;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Repositories\Collections\HierarchyRepository;

class PowerBiService
{
    /** @var HierarchyRepository */
    private $hierarchyRepository;

    public const POINTOFSALE_SLUG          = 'pointofsale_network_slug';
    public const POINTOFSALE_NETWORK_LABEL = 'pointofsale_network_label';
    public const POINTOFSALE_CNPJ          = 'pointofsale_cnpj';
    public const USER_CPF                  = 'user_cpf';
    public const DLAD_NOME                 = 'nome';
    public const SPECIALIST_COMMISSION_TIM = 'nome_lad';
    private const TELEPHONY_NAME           = 'pointOfSaleNetworkCompanyName';
    public const DMANAGEMENT_NETWORK       = 'Rede';

    public function __construct(HierarchyRepository $hierarchyRepository)
    {
        $this->hierarchyRepository = $hierarchyRepository;
    }

    public function getFilters(User $user, ?string $reportFilterName = null): array
    {
        $filters = [];

        /* Check if exists reportFilterName to mount specific array filter. */
        if ($reportFilterName !== null) {
            return $this->filterByReportName($user, $reportFilterName);
        }

        if ($user->hasPermission(SalePermission::getFullName(SalePermission::CONTEXT_ALL))) {
            $filters[] = [
                'filter' => self::POINTOFSALE_CNPJ,
                'values' => []
            ];
            return $filters;
        }

        $userSeeSalesBasedOnHierarchy = $user->hasPermission(SalePermission::getFullName(SalePermission::CONTEXT_HIERARCHY));

        $pointsOfSaleCollection = $this
            ->hierarchyRepository
            ->getPointsOfSaleThatBelongsToUser($user);


        $cnpjs = $pointsOfSaleCollection
            ->pluck('cnpj')
            ->toArray();

        if ($userSeeSalesBasedOnHierarchy) {
            $filters[] = [
                'filter' => self::POINTOFSALE_CNPJ,
                'values' => $cnpjs
            ];
            return $filters;
        }

        $filters[] = [
            'filter' => self::USER_CPF,
            'values' => [$user->cpf]
        ];
        return $filters;
    }

    private function filterByReportName(User $user, ?string $reportFilterName = null): array
    {
        if ($reportFilterName === PowerBiDashboards::TELEPHONY[PowerBiDashboards::NAME]) {
            return $this->mountFiltersToTelephony($user);
        }

        if ($reportFilterName === PowerBiDashboards::LADS[PowerBiDashboards::NAME]) {
            return $this->mountFiltersToLads($user);
        }

        if ($reportFilterName === PowerBiDashboards::MANAGEMENT[PowerBiDashboards::NAME]) {
            return $this->mountFiltersToManagement($user);
        }

        if ($reportFilterName === PowerBiDashboards::COMMISSION_TIM[PowerBiDashboards::NAME]) {
            return $this->mountFiltersToCommissionTimLads($user);
        }

        if ($reportFilterName === PowerBiDashboards::MCAFEE[PowerBiDashboards::NAME]) {
            return $this->mountFiltersToMcafee($user);
        }

        return [];
    }

    /**
     * @return array
     */
    private function mountFiltersToLads(User $user): array
    {
        if ($user->hasPermission(DashboardLadsPermission::getFullName(DashboardLadsPermission::VIEW_ALL))) {
            return[];
        }

        if ($user->role->slug !== Role::ADMIN_TAO) {
            $userFullName = $user->firstName . ' ' . $user->lastName;
            $filters[]    = [
                'filter' => self::DLAD_NOME,
                'values' => [strtoupper($userFullName)]
            ];
            return $filters;
        }

        return [];
    }

    /**
     * @return mixed[]
     */
    private function mountFiltersToTelephony(User $user): array
    {
        if ($user->role->slug !== Role::ADMIN_TAO) {
            return [
                [
                    'filter' => self::TELEPHONY_NAME,
                    'values' => [strtoupper($user->role->network->companyName ?? '')]
                ]
            ];
        }

        return [];
    }

    /**
     * @return mixed[]
     */
    private function mountFiltersToManagement(User $user): array
    {
        $networkLabel = $user->role->network->companyName ?? '';

        if ($user->role->slug === Role::ADMIN_TAO) {
            return [];
        }

        $filters[] = [
            'filter' => self::DMANAGEMENT_NETWORK,
            'values' => [strtoupper($networkLabel)]
        ];

        return $filters;
    }

    /** @return mixed[] */
    private function mountFiltersToCommissionTimLads(User $user): array
    {
        if ($user->role->slug !== Role::ADMIN_TAO) {
            $userFullName = $user->firstName . ' ' . $user->lastName;
            $filters[]    = [
                'filter' => self::SPECIALIST_COMMISSION_TIM,
                'values' => [strtoupper($userFullName)]
            ];
            return $filters;
        }

        return [];
    }

    /** @return mixed[] */
    private function mountFiltersToMcafee(User $user): array
    {
        $network = $user->getNetwork()->slug;

        if ($user->hasPermission(Permissions::DASHBOARD_MCAFEE_ALL)) {
            return [];
        }

        return [
            [
                'filter' => self::POINTOFSALE_NETWORK_LABEL,
                'values' => [strtoupper($network)]
            ]
        ];
    }
}
