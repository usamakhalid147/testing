<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GuestAccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('guest_accesses')->delete();

		DB::table('guest_accesses')->insert([
			['id' => 1, 'name' => '{"en":"Kitchen"}', 'description' => NULL, 'status' => 1],
			['id' => 2, 'name' => '{"en":"Laundry – washing machine"}', 'description' => NULL, 'status' => 1],
			['id' => 3, 'name' => '{"en":"Elevator"}', 'description' => '{"en":"Elevator"}', 'status' => 1],
			['id' => 4, 'name' => '{"en":"Parking"}', 'description' => '{"en":"Parking Near By Location"}', 'status' => 1],
			['id' => 5, 'name' => '{"en":"Pool"}', 'description' => NULL, 'status' => 1],
			['id' => 6, 'name' => '{"en":"Hot tub"}', 'description' => NULL, 'status' => 1],
		]);
    }
}
