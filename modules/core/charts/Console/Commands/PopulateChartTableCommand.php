<?php


namespace Core\Charts\Console\Commands;

use Core\Charts\Constants\ChartType;
use Core\Charts\Model\Chart;
use Core\Charts\Model\ChartRole;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use TradeAppOne\Domain\Models\Tables\Role;

class PopulateChartTableCommand extends Command
{
    protected $name        = 'chart:populate';
    protected $signature   = 'chart:populate';
    protected $description = 'Populate chart and chartRole table with dashboardPermissions';

    public function handle()
    {
        $roles = Role::all()->sortByDesc('networkId');

        $this->iterateToSave($roles);
    }

    public function iterateToSave(Collection $roles)
    {
        foreach ($roles as $role) {
            if ($role->dashboardPermissions) {
                foreach ($role->dashboardPermissions as $permission) {
                    $chartNotExist = Chart::query()->where('slug', '=', $permission['slug'])->get()->isEmpty();

                    if ($chartNotExist) {
                        $adapterChart     = [];
                        $adapterChartRole = [];

                        $chart = new Chart();
                        $chart->fill(array_merge($adapterChart, $this->adapterLineChart($permission, $role)));
                        $chart->save();

                        $chartRole = new ChartRole();
                        $chartRole->fill(array_merge($adapterChartRole, $this->adapterLineChartRole($permission, $role, $chart)));
                        $chartRole->save();
                    }
                }
            }
        }
    }

    public function adapterLineChart(array $permission, Role $role): array
    {
        $lineChart = [
            'slug'        => $permission['slug'],
            'name'        =>  strtolower(str_replace('_', ' ', $permission['slug'])),
            'description' => strtolower(str_replace('_', ' ', $permission['slug'])),
            'type'        => $role->networkId == 1 ? ChartType::ADMIN : ChartType::COMMON,
        ];

        return $lineChart;
    }

    public function adapterLineChartRole(array $permission, Role $role, Chart $chart): array
    {
        $lineChartRole = [
            'size'    => $permission['size'],
            'order'   => $permission['order'],
            'chartId' => $chart->id,
            'roleId'  => $role->id
        ];

        return $lineChartRole;
    }
}
