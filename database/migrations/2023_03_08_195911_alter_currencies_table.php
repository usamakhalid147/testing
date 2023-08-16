<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('currencies', function (Blueprint $table) {
            $table->decimal('rate', 65, 8)->change();
        });
        Schema::table('hotels', function (Blueprint $table) {
            $table->decimal('service_charge', 65, 8)->change();
            $table->decimal('property_tax', 65, 8)->change();
            $table->decimal('rating', 65, 8)->change();
        });
        Schema::table('hotel_room_prices', function (Blueprint $table) {
            $table->decimal('price', 65, 8)->change();
            $table->decimal('adult_price', 65, 8)->change();
            $table->decimal('children_price', 65, 8)->change();
        });
        Schema::table('hotel_room_price_rules', function (Blueprint $table) {
            $table->decimal('price', 65, 8)->change();
        });
        Schema::table('hotel_room_promotions', function (Blueprint $table) {
            $table->decimal('value', 65, 8)->change();
        });
        Schema::table('payouts', function (Blueprint $table) {
            $table->decimal('amount', 65, 8)->change();
            $table->decimal('penalty', 65, 8)->change();
        });
        Schema::table('reservations', function (Blueprint $table) {
            $table->decimal('coupon_price', 65, 8)->change();
            $table->decimal('cleaning_fee', 65, 8)->change();
            $table->decimal('service_fee', 65, 8)->change();
            $table->decimal('sub_total', 65, 8)->change();
            $table->decimal('host_fee', 65, 8)->change();
            $table->decimal('service_charge', 65, 8)->change();
            $table->decimal('extra_charges', 65, 8)->change();
            $table->decimal('promotion_amount', 65, 8)->change();
            $table->decimal('property_tax', 65, 8)->change();
            $table->decimal('total', 65, 8)->change();
            $table->decimal('security_fee', 65, 8)->change();
            $table->decimal('host_penalty', 65, 8)->change();
        });
        Schema::table('room_reservations', function (Blueprint $table) {
            $table->decimal('day_price', 65, 8)->change();
            $table->decimal('total_days_price', 65, 8)->change();
            $table->decimal('extra_adults_amount', 65, 8)->change();
            $table->decimal('extra_children_amount', 65, 8)->change();
            $table->decimal('meal_plan_amount', 65, 8)->change();
            $table->decimal('extra_bed_amount', 65, 8)->change();
            $table->decimal('promotion_amount', 65, 8)->change();
            $table->decimal('coupon_price', 65, 8)->change();
            $table->decimal('sub_total', 65, 8)->change();
            $table->decimal('property_tax', 65, 8)->change();
            $table->decimal('service_charge', 65, 8)->change();
            $table->decimal('service_fee', 65, 8)->change();
            $table->decimal('total_price', 65, 8)->change();
            $table->decimal('guest_refund_amount', 65, 8)->change();
            $table->decimal('host_payout_amount', 65, 8)->change();
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('amount', 65, 8)->change();
        });
        Schema::table('user_penalties', function (Blueprint $table) {
            $table->decimal('total', 65, 8)->change();
            $table->decimal('paid', 65, 8)->change();
            $table->decimal('remaining', 65, 8)->change();
        });

    }

    public function down()
    {
        Schema::table('currencies', function (Blueprint $table) {
            $table->decimal('rate', 65, 3)->change();
        });
        Schema::table('hotels', function (Blueprint $table) {
            $table->decimal('service_charge', 65, 3)->change();
            $table->decimal('property_tax', 65, 3)->change();
            $table->decimal('rating', 65, 3)->change();
        });
        Schema::table('hotel_room_prices', function (Blueprint $table) {
            $table->decimal('price', 65, 3)->change();
            $table->decimal('adult_price', 65, 3)->change();
            $table->decimal('children_price', 65, 3)->change();
        });
        Schema::table('hotel_room_price_rules', function (Blueprint $table) {
            $table->decimal('price', 65, 8)->change();
        });
        Schema::table('hotel_room_promotions', function (Blueprint $table) {
            $table->decimal('value', 65, 8)->change();
        });
        Schema::table('payouts', function (Blueprint $table) {
            $table->decimal('amount', 65, 8)->change();
            $table->decimal('penalty', 65, 8)->change();
        });
        Schema::table('reservations', function (Blueprint $table) {
            $table->decimal('coupon_price', 65, 8)->change();
            $table->decimal('cleaning_fee', 65, 8)->change();
            $table->decimal('service_fee', 65, 8)->change();
            $table->decimal('sub_total', 65, 8)->change();
            $table->decimal('host_fee', 65, 8)->change();
            $table->decimal('service_charge', 65, 8)->change();
            $table->decimal('extra_charges', 65, 8)->change();
            $table->decimal('promotion_amount', 65, 8)->change();
            $table->decimal('property_tax', 65, 8)->change();
            $table->decimal('total', 65, 8)->change();
            $table->decimal('security_fee', 65, 8)->change();
            $table->decimal('host_penalty', 65, 8)->change();
        });
        Schema::table('room_reservations', function (Blueprint $table) {
            $table->decimal('extra_adults', 65, 8)->change();
            $table->decimal('extra_children', 65, 8)->change();
            $table->decimal('day_price', 65, 8)->change();
            $table->decimal('total_days_price', 65, 8)->change();
            $table->decimal('extra_adults_amount', 65, 8)->change();
            $table->decimal('extra_children_amount', 65, 8)->change();
            $table->decimal('meal_plan_amount', 65, 8)->change();
            $table->decimal('extra_bed_amount', 65, 8)->change();
            $table->decimal('promotion_amount', 65, 8)->change();
            $table->decimal('coupon_price', 65, 8)->change();
            $table->decimal('sub_total', 65, 8)->change();
            $table->decimal('property_tax', 65, 8)->change();
            $table->decimal('service_charge', 65, 8)->change();
            $table->decimal('service_fee', 65, 8)->change();
            $table->decimal('total_price', 65, 8)->change();
            $table->decimal('guest_refund_amount', 65, 8)->change();
            $table->decimal('host_payout_amount', 65, 8)->change();
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('amount', 65, 8)->change();
        });
         Schema::table('user_penalties', function (Blueprint $table) {
            $table->decimal('total', 65, 8)->change();
            $table->decimal('paid', 65, 8)->change();
            $table->decimal('remaining', 65, 8)->change();
        });
    }
};
