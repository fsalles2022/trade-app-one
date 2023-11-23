<?php

namespace ClaroBR\Tests\Helpers;

use ClaroBR\Models\PlanClaro;
use ClaroBR\Models\PromotionsClaro;

class ClaroPosPagoPlansFixture
{
    public static function posPagoMapped()
    {
        $payload = [
            "product" => '61',
            "label" => "Claro Pós 7GB 2018",
            "price" => 119.99,
            "operator" => 'CLARO',
            "operation" => 'CLARO_POS',
            "areaCode" => 11,
            "mode" => "ACTIVATION",
            "invoiceTypes" => [
                "EMAIL",
                "VIA_POSTAL",
                "DEBITO_AUTOMATICO"
            ],
            "promotions" => [
                "product" => 194,
                "label" => "CLARO POS PLAY_SEM PROMOCAO - 79166",
                "price" => "0.00",
                "mode" => "ACTIVATION",
                "loyalty" => 1,
                "penalty" => "0.00",
                "needDevice" => false,
            ]
        ];

        $plan = new PlanClaro($payload['product'], $payload['label'], $payload['price'], []);

        $plan->operation    = $payload['operation'];
        $plan->operator     = $payload['operator'];
        $plan->invoiceTypes = $payload['invoiceTypes'];
        $plan->areaCode     = $payload['areaCode'];
        $plan->mode         = $payload['mode'];

        $promotion             = new PromotionsClaro();
        $promotion->id         = $payload['promotions']['product'];
        $promotion->label      = $payload['promotions']['label'];
        $promotion->price      = $payload['promotions']['price'];
        $promotion->mode       = $payload['promotions']['mode'];
        $promotion->loyalty    = $payload['promotions']['loyalty'];
        $promotion->penalty    = $payload['promotions']['penalty'];
        $promotion->needDevice = $payload['promotions']['needDevice'];

        $plan->promotion = $promotion;

        return $plan;
    }

    public static function posPagoFromClaro()
    {
        return json_decode('[
      {
        "id": 61,
        "nome": "CLARO_POS_7GB_2018",
        "label": "Claro Pós 7GB 2018",
        "codigo_operadora": "19322",
        "plano_tipo_id": 4,
        "ativo": 1,
        "descricao": "Claro Pós 7GB 2018 - 19322",
        "created_at": "2018-10-23 17:27:54",
        "updated_at": "2019-01-30 10:29:14",
        "pontuacao": 0,
        "faturas": {
          "EMAIL": "Email",
          "VIA_POSTAL": "Via Postal",
          "DEBITO_AUTOMATICO": "Débito Automático"
        },
        "numero_dependentes": 1,
        "plan_type": {
          "id": 4,
          "label": "Pós Pago",
          "nome": "POS_PAGO",
          "ativo": 1,
          "operadora_id": 1,
          "created_at": "2017-04-20 20:38:53",
          "updated_at": "2017-04-20 20:38:53"
        },
        "plans_area_code": [
          {
            "id": 17826,
            "ddd": 11,
            "plano_id": 61,
            "valor": "119.99",
            "ativo": 1,
            "created_at": "2019-01-30 10:29:14",
            "updated_at": "2019-01-30 10:29:14",
            "promotions": [
              {
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
              }
            ]
          }
        ]
      }]', true);
    }
}
