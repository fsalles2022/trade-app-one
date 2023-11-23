<?php

namespace Core\HandBooks\Tests\Feature;

use Core\HandBooks\Models\Handbook;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class HandbookDomainsFeatureTest extends TestCase
{
    use AuthHelper;

    public const ROUTE = 'handbooks/domains';

    /** @test */
    public function get_should_return_correct_structure_domains()
    {
        $response = $this->authAs()->get(self::ROUTE);

        $response->assertJsonStructure([
           '*' => [
               'id', 'label', 'categories'
           ]
        ]);
    }

    /** @test */
    public function get_should_return_categories_already_registred_domains()
    {
        $user = (new UserBuilder())->build();

        factory(Handbook::class)->create([
            'userId' => $user->id,
            'module' => Operations::TELECOMMUNICATION,
            'category' => 'category-1'
        ]);

        factory(Handbook::class)->create([
            'userId' => $user->id,
            'module' => Operations::TELECOMMUNICATION,
            'category' => 'category-3'
        ]);

        factory(Handbook::class)->create([
            'userId' => $user->id,
            'module' => Operations::SECURITY,
            'category' => 'category-2'
        ]);

        $response = $this->authAs()->get(self::ROUTE);

        $telecomm = array_where($response->json(), static function ($value, $key) {
            return $value['id'] === Operations::TELECOMMUNICATION;
        });

        $security = array_where($response->json(), static function ($value, $key) {
            return $value['id'] === Operations::SECURITY;
        });

        $this->assertEquals('category-1', array_first($telecomm)['categories'][0]);
        $this->assertEquals('category-3', array_first($telecomm)['categories'][1]);
        $this->assertEquals('category-2', array_first($security)['categories'][0]);
    }
}
