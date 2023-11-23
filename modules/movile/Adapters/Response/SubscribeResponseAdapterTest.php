<?php

namespace Movile\Adapters\Response;

use GuzzleHttp\Psr7\Response;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Tests\TestCase;
use function GuzzleHttp\Psr7\stream_for;

class SubscribeResponseAdapterTest extends TestCase
{
    /** @test */
    public function should_return_status_412_when_msisdn_invalid()
    {
        $stream       = stream_for('{
                    "status" : 500, 
                    "message" : "Error while performing sign up", 
                    "transaction_id" : "cc83cf84-d5f0-42c0-a26c-990a3c5c0298"
                }');
        $mockResponse = new Response(500, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new SubscribeResponseAdapter($response);

        $response = $adapted->getAdapted();
        self::assertEquals(\Illuminate\Http\Response::HTTP_PRECONDITION_FAILED, $adapted->getStatus());
        self::assertEquals(
            trans('movile::messages.subscription.msisdn_invalid'),
            data_get($response, 'errors.0.message')
        );
        self::assertFalse($adapted->isSuccess());
    }

    /** @test */
    public function should_return_message_when_msisdn_invalid()
    {
        $stream       = stream_for('{
                    "status" : 500, 
                    "message" : "Error while performing sign up", 
                    "transaction_id" : "cc83cf84-d5f0-42c0-a26c-990a3c5c0298"
                }');
        $mockResponse = new Response(500, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new SubscribeResponseAdapter($response);

        $response = $adapted->getAdapted();
        self::assertEquals(
            trans('movile::messages.subscription.msisdn_invalid'),
            data_get($response, 'errors.0.message')
        );
    }

    /** @test */
    public function should_return_false_success_when_http_status_is_200_and_success_structire_not_found()
    {
        $stream       = stream_for('{
                    "status" : 500, 
                    "message" : "Error while performing sign up", 
                    "transaction_id" : "cc83cf84-d5f0-42c0-a26c-990a3c5c0298"
                }');
        $mockResponse = new Response(200, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new SubscribeResponseAdapter($response);
        $response     = $adapted->getAdapted();

        self::assertEquals(
            trans('movile::messages.subscription.msisdn_invalid'),
            data_get($response, 'errors.0.message')
        );
    }

    /** @test */
    public function should_return_false_succes_when_succes_structure_not_found()
    {
        $stream       = stream_for('{
                    "status" : 500, 
                    "message" : "Error while performing sign up", 
                    "transaction_id" : "cc83cf84-d5f0-42c0-a26c-990a3c5c0298"
                }');
        $mockResponse = new Response(500, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new SubscribeResponseAdapter($response);

        self::assertFalse($adapted->isSuccess());
    }

    /** @test */
    public function should_return_error_message()
    {
        $stream       = stream_for('{
                    "status" : 500, 
                    "message" : "Eing sign up", 
                    "transaction_id" : "cc83cf84-d5f0-42c0-a26c-990a3c5c0298"
                }');
        $mockResponse = new Response(200, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new SubscribeResponseAdapter($response);

        $response = $adapted->getAdapted();
        self::assertFalse($adapted->isSuccess());
    }

    /** @test */
    public function should_return_success()
    {
        $stream       = stream_for('{"subscription_id":2709417418893174,"account_id":"2eef6779-05ba-47fd-a065-a315de3fbf7b","benefit":{"id":"a7d23226-bdfc-4d85-a30a-b2d7eccc36e6","phone_number":5511973512530,"sku":"com.movile.cubes.br.biweekly.homolog","origin":"trade_up_group","application_id":437}}');
        $mockResponse = new Response(200, [], $stream);
        
        $response = RestResponse::success($mockResponse);
        $adapted  = new SubscribeResponseAdapter($response);

        self::assertTrue($adapted->isSuccess());
    }
}
