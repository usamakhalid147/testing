<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HotelRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('hotel_rules')->delete();

		DB::table('hotel_rules')->insert([
			['id' => 1, 'name' => '{"en":"No Open Flame"}', 'description' => NULL, 'status' => 1],
			['id' => 2, 'name' => '{"en":"No Smoking"}', 'description' => NULL, 'status' => 1],
			['id' => 3, 'name' => '{"en":"No Cooking"}', 'description' => NULL, 'status' => 1],
			['id' => 4, 'name' => '{"en":"No Loud Music"}', 'description' => '{"en":"Music Allowed upto 90dB"}', 'status' => 1],
			['id' => 5, 'name' => '{"en":"No Dancing"}', 'description' => NULL, 'status' => 1],
			['id' => 6, 'name' => '{"en":"No Late Night Parties"}', 'description' => NULL, 'status' => 1],
			['id' => 7, 'name' => '{"en":"No Teenagers (10-18)"}', 'description' => '{"en":"Kids Not Allowed"}', 'status' => 1],
		]);
    }
}
