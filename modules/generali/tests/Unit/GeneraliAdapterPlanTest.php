<?php


namespace Generali\tests\Unit;

use Generali\Adapters\GeneraliAdapterPlan;
use Generali\Exceptions\GeneraliExceptions;
use Generali\Models\GeneraliProduct;
use TradeAppOne\Tests\TestCase;

class GeneraliAdapterPlanTest extends TestCase
{
    /** @test */
    public function should_return_a_correct_structure_plan(): void
    {
        factory(GeneraliProduct::class)->create();

        $eligibility = json_decode(
            file_get_contents(__DIR__ . '/../ServerMock/response/eligibility.json'),
            true
        );

        $product = collect($eligibility)->where('produto_parceiro_id', '132')->toArray();
        $plan    = GeneraliAdapterPlan::run($product, ['devicePrice' => 750.01, 'slug' => 'SMARTPHONE']);

        $this->assertArrayHasKey('plans', $plan[0]);
        $this->assertArrayHasKey('valor_premio_bruto', $plan[0]['plans'][0]);
        $this->assertArrayHasKey('equipamento_de_para', $plan[0]['plans'][0]);
    }
}
