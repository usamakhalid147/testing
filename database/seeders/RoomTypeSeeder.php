<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('room_types')->delete();

		DB::table('room_types')->insert([
			['id' => 1, 'name' => '{"en":"Entire Place"}', 'description' => '{"en":"Guests have the whole place to themselfs. This usually includes a bedroom, a bathroom and a kitchen"}', 'image' => 'entire_place.png', 'status' => 1],
			['id' => 2, 'name' => '{"en":"Private Room"}', 'description' => '{"en":"Guests have their own private room for sleeping. Other areas could be shared"}', 'image' => 'private_room.png', 'status' => 1],
		]);
    }
}
