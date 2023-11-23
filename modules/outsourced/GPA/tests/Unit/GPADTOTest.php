<?php


namespace Outsourced\GPA\tests\Unit;

use Outsourced\Enums\Outsourced;
use Outsourced\GPA\Adapters\Request\ActivationAdapter;
use Outsourced\GPA\tests\GPATestHelpers;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\TestCase;

class GPADTOTest extends TestCase
{
    /** @test */
    public function should_return_a_correct_structure_to_hooks_send(): void
    {
        $pointOfSale = factory(PointOfSale::class)->make([
            'slug' => 0,
            'areaCode' => 35,
            'state' => 'SP',
            'networkId' => (new NetworkBuilder())->withSlug(Outsourced::GPA)->build()->id,
        ]);

        $structure           = GPATestHelpers::service();
        $structure['status'] = ServiceStatus::APPROVED;

        $sale = factory(Sale::class)->create([
            'services' => [$structure],
            'updatedAt' => '2021-03-23 19:22:37',
            'createdAt' => '2021-03-23 19:22:37',
        ]);
        
        $sale->user        = factory(User::class)->make(['cpf' => '72728681554'])->toArray();
        $sale->pointOfSale = $pointOfSale->toArray();
        $sale->touch();

        $file      = __DIR__ . '/correct_payload_structure.json';
        $structure = (new ActivationAdapter($sale->services->first()))->toArray();

        self::assertJsonStringEqualsJsonFile($file, json_encode($structure));
    }
}
