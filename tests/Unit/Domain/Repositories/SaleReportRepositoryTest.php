<?php

namespace TradeAppOne\Tests\Unit\Domain\Repositories;

use Illuminate\Support\Facades\Auth;
use Reports\Tests\Fixture\ElasticSearchTaoFixture;
use Reports\Tests\Helpers\ElasticSearchHelper;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;
use TradeAppOne\Domain\Components\Elasticsearch\Interfaces\ElasticConnection;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Factories\MongoDbConnector;
use TradeAppOne\Domain\Models\Tables\Operator;
use TradeAppOne\Domain\Repositories\Collections\HierarchyRepository;
use TradeAppOne\Domain\Repositories\Collections\SaleReportRepository;
use TradeAppOne\Domain\Repositories\Collections\UserRepository;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class SaleReportRepositoryTest extends TestCase
{
    use AuthHelper, ElasticSearchHelper;

    public const MODULE = 'SALE';

    /** @test */
    public function should_return_an_instance_of_sale_report_repository(): void
    {
        $saleReportRepository = new SaleReportRepository(
            app()->make(ElasticConnection::class),
            app()->make(HierarchyRepository::class),
            app()->make(UserRepository::class),
            app()->make(MongoDbConnector::class)
        );

        $className            = get_class($saleReportRepository);
        $this->assertEquals(SaleReportRepository::class, $className);
    }

    /** @test */
    public function must_return_promoter_context(): void
    {
        $elasticFixture = ElasticSearchTaoFixture::getSaleArray();
        $this->mockElasticSearchConnection($elasticFixture);

        $operator = Operator::create([
            'slug' => Operations::VIVO
        ]);

        $user = UserBuilder::make()
            ->withOperators($operator)
            ->build();

        Auth::setUser($user);

        $query = new ElasticsearchQueryBuilder();
        //@TODO: Verifica como testar que o retorno foi para um promotor a partir de seu operador
        $received = $this->repository()->getFilteredByContextUsingScroll($query);
        self::assertNotEmpty($received);
    }

    private function repository(): SaleReportRepository
    {
        return resolve(SaleReportRepository::class);
    }
}
