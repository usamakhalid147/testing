<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TruncateAndPopulateTablesSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // truncate the specified tables
        DB::table('hotel_room_prices')->truncate();
        DB::table('room_reservations')->truncate();
        DB::table('review_photos')->truncate();
        DB::table('reviews')->truncate();
        DB::table('reservations')->truncate();
        DB::table('hotels')->truncate();
        DB::table('payouts')->truncate();
        DB::table('hotel_addresses')->truncate();
        DB::table('hotel_rooms')->truncate();
        DB::table('hotel_photos')->truncate();
        DB::table('hotel_room_beds')->truncate();
        DB::table('hotel_room_calendars')->truncate();
        DB::table('hotel_room_photos')->truncate();
        DB::table('hotel_room_promotions')->truncate();
        DB::table('currencies')->truncate();
        DB::table('coupon_codes')->truncate();
        DB::table('transactions')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        // populate the currencies table
        DB::table('currencies')->insert([
            [
                'id' => 1,
                'name' => 'Vietnam Dong',
                'code' => 'VND',
                'symbol' => '₫',
                'rate' => 1,
                'status' => 1,
            ],
        ]);
        DB::table('users')->update(['user_currency' => 'VND']);
        // Set user_currency to VND for all admins
        DB::table('admins')->update(['user_currency' => 'VND']);
    }
}
