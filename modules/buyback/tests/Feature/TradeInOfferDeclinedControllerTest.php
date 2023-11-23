<?php

use Buyback\Models\OfferDeclined;
use Buyback\Tests\Helpers\Builders\OfferDeclinedBuilder;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use Illuminate\Http\Response;
use TradeAppOne\Tests\TestCase;

class TradeInOfferDeclinedControllerTest extends TestCase
{
    use AuthHelper;

    protected function setUp()
    {
        parent::setUp();
        OfferDeclined::query()->forceDelete();
    }

    /** @test */
    public function declined_offers_should_response_with_status_200_and_a_valid_structure_when_there_are_withdrawals()
    {
        $userHelper = (new UserBuilder())->build();
        (new OfferDeclinedBuilder())->withUser($userHelper)->build();
        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->getJson('buyback/offer_declined');
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                [
                    'customer' => [
                        'fullName',
                        'email',
                        'mainPhone'
                    ],
                    'device' => [
                        'label',
                        'price',
                        'note'
                    ],
                    'questions' => [
                        [
                            'question',
                            'answer'
                        ]
                    ],
                    'reason',
                    'pointOfSale',
                    'user' => [
                        'firstName',
                        'lastName',
                        'email',
                        'cpf',
                    ],
                    'createdAt'
                ]
            ]
        ]);
    }

    /** @test */
    public function declined_offers_import_should_response_with_status_200_and_a_valid_content()
    {
        $userHelper    = (new UserBuilder())->build();
        $offerDeclined = (new OfferDeclinedBuilder())->withUser($userHelper)->build();
        $date          = $offerDeclined->createdAt;

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->getJson('buyback/offer_declined/export');
        $response->assertStatus(Response::HTTP_OK);
        $content = $response->content();
        $this->assertContains($offerDeclined->pointOfSale['slug'], $content);
        $this->assertContains($offerDeclined->operator, $content);
        $this->assertContains($offerDeclined->operation, $content);
        $this->assertContains($offerDeclined->customer['fullName'], $content);
        $this->assertContains($offerDeclined->customer['mainPhone'], $content);
        $this->assertContains($offerDeclined->customer['email'], $content);
        $this->assertContains($offerDeclined->device['label'], $content);
        $this->assertContains($offerDeclined->device['model'], $content);
        $this->assertContains($offerDeclined->device['brand'], $content);
        $this->assertContains($offerDeclined->device['storage'], $content);
        $this->assertContains((String) $offerDeclined->device['price'], $content);
        $this->assertContains((String) $offerDeclined->device['note'], $content);
        $this->assertContains($offerDeclined->device['imei'], $content);
        $this->assertContains($offerDeclined->reason, $content);
        $this->assertContains((String) $date->format('d-m-Y H:i'), $content);
    }
}
