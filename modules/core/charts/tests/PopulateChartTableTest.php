<?php

use Core\Charts\Constants\ChartType;
use Core\Charts\Model\Chart;
use Illuminate\Support\Facades\Artisan;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Tests\TestCase;

class PopulateChartTableTest extends TestCase
{
    /** @test */
    public function should_call_and_execute_command_to_populate_chart()
    {
        $networkAdmin = $this->factoryNetwork(1);
        $roleAdmin    = $this->factoryRole($networkAdmin->id, json_encode($this->dashboardPermissionsAdmin()));

        $networkCommon = $this->factoryNetwork(3);
        $roleCommon    = $this->factoryRole($networkCommon->id, json_encode($this->dashboardPermissionsCommon()));

        Artisan::call('chart:populate');

        $this->assertDatabaseHas('charts', [
            'slug' => $roleCommon->dashboardPermissions['0']['slug'],
            'name' => strtolower(str_replace('_', ' ', $roleCommon->dashboardPermissions['0']['slug'])),
            'type' => ChartType::COMMON
        ]);

        $this->assertDatabaseHas('charts', [
            'slug' => $roleAdmin->dashboardPermissions['0']['slug'],
            'name' => strtolower(str_replace('_', ' ', $roleAdmin->dashboardPermissions['0']['slug'])),
            'type' => ChartType::ADMIN
        ]);

        $chart = Chart::find(2);

        $this->assertDatabaseHas('chart_roles', [
            'size'    => 2,
            'order'   => 2,
            'chartId' => $chart->id,
            'roleId'  => $roleAdmin->id
        ]);
    }

    public function dashboardPermissionsAdmin()
    {
        return [
            [
                "size"  => 2,
                "slug"  => "NUMBER_TOTAL_MONTH_ADMIN",
                "order" => 2
            ]
        ];
    }

    public function dashboardPermissionsCommon()
    {
        return [
            [
                "size"  => 2,
                "slug"  => "NUMBER_TOTAL_COMMON",
                "order" => 1
            ]
        ];
    }

    public function factoryNetwork(int $id)
    {
        return factory(Network::class)->create([
            'id' => $id,
        ]);
    }

    public function factoryRole($networkId, $dashboardPermissions)
    {
        return factory(Role::class)->create([
            'networkId'            => $networkId,
            'dashboardPermissions' => $dashboardPermissions
        ]);
    }
}
