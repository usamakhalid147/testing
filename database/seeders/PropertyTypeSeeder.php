<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropertyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('property_types')->delete();

		DB::table('property_types')->insert([
			['id' => 1, 'name' => '{"en":"Suite Hotel"}', 'image' => 'suite_hotel.png'],
			['id' => 2, 'name' => '{"en":"Inn"}', 'image' => 'inn.png'],
			['id' => 3, 'name' => '{"en":"Bunkhouse"}', 'image' => 'bunk_house.png'],
			['id' => 4, 'name' => '{"en":"Motel"}', 'image' => 'motel.png'],
			['id' => 5, 'name' => '{"en":"Residence"}', 'image' => 'residence.png'],
			['id' => 6, 'name' => '{"en":"Resort"}', 'image' => 'resort.png'],
			['id' => 7, 'name' => '{"en":"Villa"}', 'image' => 'villa.png'],
			['id' => 8, 'name' => '{"en":"Eco hotel"}', 'image' => 'eco_hotel.png'],
			['id' => 9, 'name' => '{"en":"Road house"}', 'image' => 'road_house.png'],
			['id' => 10, 'name' => '{"en":"Convention Center"}', 'image' => 'convention_center.png'],
		]);		
    }
}
