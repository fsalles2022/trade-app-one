<?php

namespace ClaroBR\Tests\Helpers\Fixture;

use ClaroBR\Models\PlanClaro;
use ClaroBR\Models\PromotionsClaro;
use TradeAppOne\Domain\Enumerators\Operations;

class ClaroBoletoPlansFixture
{

    public static function controleBoletoMapped(): PlanClaro
    {
        $payload = [
            "product" => "67",
            "label" => "Claro Controle Play 3GB + Minutos Ilimitados",
            "price" => 49.99,
            "operator" => "CLARO",
            "operation" => Operations::CLARO_CONTROLE_BOLETO,
            "areaCode" => 11,
            "mode" => "MIGRATION",
            "invoiceTypes" => [
                "EMAIL",
                "VIA_POSTAL",
                "DEBITO_AUTOMATICO"
            ],
            "promotions" => [
                "product" => 192,
                "label" => "CONTROLE - SEM PROMOÇÃO - 79146",
                "price" => "0.00",
                "mode" => "MIGRATION",
                "loyalty" => 0,
                "penalty" => "0.00",
                "needDevice" => false,

            ]
        ];

        $plan = new PlanClaro($payload['product'], $payload['label'], $payload['price'], []);

        $plan->operation    = $payload['operation'];
        $plan->operator     = $payload['operator'];
        $plan->invoiceTypes = $payload['invoiceTypes'];
        $plan->areaCode     = $payload['areaCode'];
        $plan->promotions   = $payload['promotions'];
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

    public static function controleBoletoFromClaro()
    {

        return json_decode('[{
			"id": 67,
			"nome": "CLARO_CONTROLE_PLAY_3GB_+_MINUTOS_ILIMITADOS",
			"label": "Claro Controle Play 3GB + Minutos Ilimitados",
			"codigo_operadora": "19303",
			"plano_tipo_id": 1,
			"ativo": 1,
			"descricao": "Liga\u00e7\u00f5es ilimitadas para qualquer operadora do Brasil, usando 21.",
			"created_at": "2018-11-16 17:43:35",
			"updated_at": "2018-11-19 19:51:28",
			"pontuacao": 0,
			"faturas": {
				"EMAIL": "Email",
				"VIA_POSTAL": "Via Postal",
				"DEBITO_AUTOMATICO": "D\u00e9bito Autom\u00e1tico"
			},
			"numero_dependentes": null,
			"plan_type": {
				"id": 1,
				"label": "Controle Boleto",
				"nome": "CONTROLE_BOLETO",
				"ativo": 1,
				"operadora_id": 1,
				"created_at": "2017-04-20 20:38:53",
				"updated_at": "2017-04-20 20:38:53"
			},
			"plans_area_code": [{
				"id": 13497,
				"ddd": 11,
				"plano_id": 67,
				"valor": "49.99",
				"ativo": 1,
				"created_at": "2018-11-19 19:51:28",
				"updated_at": "2018-11-19 19:51:28",
				"promotions": [{
					"id": 192,
					"nome": "CONTROLE - SEM PROMO\u00c7\u00c3O - 79146",
					"codigo_operadora": "79146",
					"ativo": 1,
					"valor": "0.00",
					"created_at": "2018-11-19 08:35:00",
					"updated_at": "2018-11-19 08:35:00",
					"categoria": "MIGRACAO",
					"plano_tipo_id": 1,
					"requer_aparelho": 0,
					"fidelidade": 0,
					"multa": "0.00",
					"pivot": {
						"plano_ddd_id": 13497,
						"promocao_id": 192
					}
				}]
			}]
		}, {
			"id": 68,
			"nome": "CLARO_CONTROLE_PLAY_4GB_+_MINUTOS_ILIMITADOS",
			"label": "Claro Controle Play 4GB + Minutos Ilimitados",
			"codigo_operadora": "19305",
			"plano_tipo_id": 1,
			"ativo": 1,
			"descricao": "Navegar \u00e0 vontade em aplicativos de mobilidade e liga\u00e7\u00f5es ilimitadas para qualquer operadora do Brasil, usando 21.",
			"created_at": "2018-11-19 09:17:19",
			"updated_at": "2018-11-30 11:49:04",
			"pontuacao": 0,
			"faturas": {
				"EMAIL": "Email",
				"VIA_POSTAL": "Via Postal",
				"DEBITO_AUTOMATICO": "D\u00e9bito Autom\u00e1tico"
			},
			"numero_dependentes": null,
			"plan_type": {
				"id": 1,
				"label": "Controle Boleto",
				"nome": "CONTROLE_BOLETO",
				"ativo": 1,
				"operadora_id": 1,
				"created_at": "2017-04-20 20:38:53",
				"updated_at": "2017-04-20 20:38:53"
			},
			"plans_area_code": [{
				"id": 14709,
				"ddd": 11,
				"plano_id": 68,
				"valor": "59.99",
				"ativo": 1,
				"created_at": "2018-11-30 11:49:04",
				"updated_at": "2018-11-30 11:49:04",
				"promotions": [{
					"id": 220,
					"nome": "CONTROLE PLAY 4GB C\/ FIDELIDADE - 79433",
					"codigo_operadora": "79433",
					"ativo": 1,
					"valor": "-10.00",
					"created_at": "2018-11-19 08:35:00",
					"updated_at": "2018-11-19 08:35:00",
					"categoria": "ATIVACAO",
					"plano_tipo_id": 1,
					"requer_aparelho": 0,
					"fidelidade": 12,
					"multa": "120.00",
					"pivot": {
						"plano_ddd_id": 14709,
						"promocao_id": 220
					}
				}, {
					"id": 221,
					"nome": "CONTROLE - SEM PROMO\u00c7\u00c3O - 79147",
					"codigo_operadora": "79147",
					"ativo": 1,
					"valor": "0.00",
					"created_at": "2018-11-19 08:35:00",
					"updated_at": "2018-11-19 08:35:00",
					"categoria": "ATIVACAO",
					"plano_tipo_id": 1,
					"requer_aparelho": 0,
					"fidelidade": 0,
					"multa": "0.00",
					"pivot": {
						"plano_ddd_id": 14709,
						"promocao_id": 221
					}
				}]
			}]
		}, {
			"id": 69,
			"nome": "CLARO_CONTROLE_PLAY_5_GB_+_MINUTOS_ILIMITADOS",
			"label": "Claro Controle Play 5 GB + Minutos Ilimitados",
			"codigo_operadora": "19306",
			"plano_tipo_id": 1,
			"ativo": 1,
			"descricao": "Navegar \u00e0 vontade em aplicativos de mobilidade e redes sociais e liga\u00e7\u00f5es ilimitadas para qualquer operadora do Brasil, usando 21.",
			"created_at": "2018-11-19 09:22:44",
			"updated_at": "2018-11-30 11:45:18",
			"pontuacao": 0,
			"faturas": {
				"EMAIL": "Email",
				"VIA_POSTAL": "Via Postal",
				"DEBITO_AUTOMATICO": "D\u00e9bito Autom\u00e1tico"
			},
			"numero_dependentes": null,
			"plan_type": {
				"id": 1,
				"label": "Controle Boleto",
				"nome": "CONTROLE_BOLETO",
				"ativo": 1,
				"operadora_id": 1,
				"created_at": "2017-04-20 20:38:53",
				"updated_at": "2017-04-20 20:38:53"
			},
			"plans_area_code": [{
				"id": 14575,
				"ddd": 11,
				"plano_id": 69,
				"valor": "79.99",
				"ativo": 1,
				"created_at": "2018-11-30 11:45:18",
				"updated_at": "2018-11-30 11:45:18",
				"promotions": [{
					"id": 224,
					"nome": "CONTROLE PLAY 5GB C\/ FIDELIDADE - 79436",
					"codigo_operadora": "79436",
					"ativo": 1,
					"valor": "-15.00",
					"created_at": "2018-11-19 08:35:00",
					"updated_at": "2018-11-19 08:35:00",
					"categoria": "ATIVACAO",
					"plano_tipo_id": 1,
					"requer_aparelho": 0,
					"fidelidade": 12,
					"multa": "180.00",
					"pivot": {
						"plano_ddd_id": 14575,
						"promocao_id": 224
					}
				}, {
					"id": 225,
					"nome": "CONTROLE - SEM PROMO\u00c7\u00c3O - 79148",
					"codigo_operadora": "79148",
					"ativo": 1,
					"valor": "0.00",
					"created_at": "2018-11-19 08:35:00",
					"updated_at": "2018-11-19 08:35:00",
					"categoria": "ATIVACAO",
					"plano_tipo_id": 1,
					"requer_aparelho": 0,
					"fidelidade": 0,
					"multa": "0.00",
					"pivot": {
						"plano_ddd_id": 14575,
						"promocao_id": 225
					}
				}]
			}]
		}]', true);
    }
}
