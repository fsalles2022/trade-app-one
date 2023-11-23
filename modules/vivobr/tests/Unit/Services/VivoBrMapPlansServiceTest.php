<?php

namespace VivoBR\Tests\Unit\Services;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Models\Plan;
use TradeAppOne\Tests\TestCase;
use VivoBR\Services\VivoBrMapPlansService;

class VivoBrMapPlansServiceTest extends TestCase
{

    /** @test */
    public function should_map_return_filtered()
    {
        $success = $this->getPlans();
        $result  = VivoBrMapPlansService::map($success);

        $this->assertEquals(Collection::class, get_class($result));
    }

    private function getPlans()
    {
        return [
            "planos" => [
                [
                    "id" => 1,
                    "nome" => "PRÉ PAGO",
                    "ddd" => 11,
                    "valor" => 0,
                    "tipo" => "PRE",
                ]
            ]
        ];
    }

    /** @test */
    public function should_map_return_plan_object()
    {
        $success = $this->getPlans();

        $result = VivoBrMapPlansService::map($success);

        $this->assertEquals(Plan::class, get_class($result[0]));
    }

    /** @test */
    public function should_map_return_empty_collection_when_response_empty()
    {
        $response = [];

        $result = VivoBrMapPlansService::map($response);

        $this->assertEmpty($result);
    }

    /** @test */
    public function should_map_return_empty_collection_when_response_with_empty_planos()
    {
        $success = ['planos' => []];

        $result = VivoBrMapPlansService::map($success);

        $this->assertEmpty($result);
    }

    /** @test */
    public function should_map_return_empty_collection_with_incomplete_plans()
    {
        $success = [
            'planos' => [
                ['id' => 2]
            ]
        ];

        $result = VivoBrMapPlansService::map($success);

        $this->assertEmpty($result);
    }

    /** @test */
    public function should_map_return_empty_collection_with_plan_with_invalid_tipo()
    {
        $success = [
            "planos" => [
                [
                    "id" => 1,
                    "nome" => "PRÉ PAGO",
                    "ddd" => 11,
                    "valor" => 0,
                    "tipo" => "NON_EXISTENT",
                ]
            ]
        ];

        $result = VivoBrMapPlansService::map($success);

        $this->assertEmpty($result);
    }

    /** @test */
    public function should_map_return_empty_collection_with_incomplete_values()
    {
        $success = [
            'planos' => [
                [
                    'id' => 2,
                    'nome' => 2,
                    'ddd' => 2,

                ]

            ]
        ];

        $result = VivoBrMapPlansService::map($success);

        $this->assertEmpty($result);
    }

    /** @test */
    public function should_return_correct_dependents_when_type_and_name_valid()
    {
        $plans = [
            "planos" => [
                [
                    "id" => 1,
                    "nome" => "Vivo ". VivoBrMapPlansService::DEPENDENTS_RECOGNITION,
                    "ddd" => 11,
                    "valor" => 0,
                    "tipo" => 'POS_FATURA',
                ]
            ]
        ];

        $adapted = VivoBrMapPlansService::map($plans)[0];
        $this->assertEquals(5, $adapted->dependents);
    }

    /** @test */
    public function should_return_correct_dependents_when_name_not_contains_recognition()
    {
        $plans = [
            "planos" => [
                [
                    "id" => 2,
                    "nome" => "Vivo",
                    "ddd" => 11,
                    "valor" => 0,
                    "tipo" => 'POS_FATURA',
                ]
            ]
        ];

        $adapted = VivoBrMapPlansService::map($plans)[0];
        $this->assertEquals(0, $adapted->dependents);
    }

    /** @test */
    public function should_return_correct_dependents_when_type_not_is_pos()
    {
        $plans = [
            "planos" => [
                [
                    "id" => 2,
                    "nome" => "Vivo ". VivoBrMapPlansService::DEPENDENTS_RECOGNITION,
                    "ddd" => 11,
                    "valor" => 0,
                    "tipo" => 'PRE',
                ]
            ]
        ];

        $adapted = VivoBrMapPlansService::map($plans)[0];
        $this->assertEquals(0, $adapted->dependents);
    }
}
