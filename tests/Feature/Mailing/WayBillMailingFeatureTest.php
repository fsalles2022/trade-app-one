<?php

namespace TradeAppOne\Tests\Feature\Mailing;

use Buyback\Services\WaybillJob;
use Buyback\Tests\Helpers\Builders\WaybillBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use TradeAppOne\Mail\WayBillShip;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class WayBillMailingFeatureTest extends TestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    /** @test */
    public function should_send_way_bill_email_with_formated_data(): void
    {
        DB::beginTransaction();
        $user = (new UserBuilder())->build();
        $pointOfSale = $user->pointsOfSale->first();

        $waybill = (new WaybillBuilder())->withPointOfSale($pointOfSale)->build();
        $sender = new WayBillShip($waybill, [ $user->email ]);

        Mail::fake();
        Mail::send($sender->asTest());

        $sender->clearAttachs();

        DB::rollBack();
    }
}
