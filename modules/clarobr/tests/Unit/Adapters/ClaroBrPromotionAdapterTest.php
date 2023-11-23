<?php

namespace ClaroBR\Tests\Unit\Adapters;

use ClaroBR\Adapters\ClaroBrPromotionAdapter;
use ClaroBR\Models\PromotionsClaro;
use PHPUnit\Framework\TestCase;

class ClaroBrPromotionAdapterTest extends TestCase
{

    /** @test */
    public function should_return_an_instance()
    {
        $class     = new ClaroBrPromotionAdapter([]);
        $className = get_class($class);
        $this->assertEquals(ClaroBrPromotionAdapter::class, $className);
    }

    /** @test */
    public function should_adapt_return_an_instance_of_promotion()
    {
        $class = new ClaroBrPromotionAdapter($this->getPromotion());

        $result = $class->adapt();

        $this->assertInstanceOf(PromotionsClaro::class, $result);
    }

    /** @test */
    public function should_adapt_return_null_when_promotion_not_active()
    {
        $class = new ClaroBrPromotionAdapter($this->getPromotion(0));

        $result = $class->adapt();

        $this->assertNull($result);
    }

    private function getPromotion($active = 1)
    {
        $promotion =  json_decode('{
                "id": 194,
                "nome": "CLARO POS PLAY_SEM PROMOCAO - 79166",
                "codigo_operadora": "79166",
                "ativo": 1,
                "valor": "0.00",
                "created_at": "2018-11-19 09:00:00",
                "updated_at": "2018-11-19 09:00:00",
                "categoria": "ATIVACAO",
                "plano_tipo_id": 4,
                "requer_aparelho": 0,
                "fidelidade": 1,
                "multa": "0.00",
                "pivot": {
                  "plano_ddd_id": 17826,
                  "promocao_id": 194
                }
              }', true);

        $promotion['ativo'] = $active;

        return $promotion;
    }

    /** @test */
    public function should_adapt_return_null_when_construct_with_invalid_promotion()
    {
        $class = new ClaroBrPromotionAdapter([]);

        $result = $class->adapt();

        $this->assertNull($result);
    }
}
