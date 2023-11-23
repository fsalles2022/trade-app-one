<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUniqueKeyCouponViaVarejoCoupons extends Migration
{
    public function up(): void
    {
        Schema::connection('outsourced')
            ->table('via_varejo_coupons', static function (Blueprint $table) {
                $table->dropUnique('via_varejo_coupons_coupon_unique');
            });
    }

    public function down(): void
    {
        Schema::connection('outsourced')
            ->table('via_varejo_coupons', static function (Blueprint $table) {
                $table->unique('coupon');
            });
    }
}
