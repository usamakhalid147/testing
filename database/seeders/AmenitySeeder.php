<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AmenitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('amenities')->delete();
		DB::table('amenity_types')->delete();

		DB::table('amenity_types')->insert([
			['id' => '1', 'name' => '{"en":"Common Amenities"}','description' => NULL, 'separate_section' => 0, 'status' => 1],
			['id' => '2', 'name' => '{"en":"Additional Amenities"}','description' => NULL, 'separate_section' => 0, 'status' => 1],
			['id' => '3', 'name' => '{"en":"Safety amenities"}','description' => '{"en":"Check your local laws, which may require a working carbon monoxide detector in every room."}', 'separate_section' => 1, 'status' => 1],
			['id' => '4', 'name' => '{"en":"Hotel Facilities & Services"}','description' => '{"en":"Hotel Facilities & Services"}', 'separate_section' => 1, 'status' => 1],
			['id' => '5', 'name' => '{"en":"Hotel Surrounding"}','description' => '{"en":"Hotel Surrounding"}', 'separate_section' => 1, 'status' => 1],
			['id' => '6', 'name' => '{"en":"Business Services"}','description' => '{"en":"Business Services"}', 'separate_section' => 1, 'status' => 1],
			['id' => '7', 'name' => '{"en":"Cleaning Services"}','description' => '{"en":"Cleaning Services"}', 'separate_section' => 1, 'status' => 1],
			['id' => '8', 'name' => '{"en":"Miscellaneous"}','description' => '{"en":"Miscellaneous"}', 'separate_section' => 1, 'status' => 1],
		]);

		DB::table('amenities')->insert([
			['id' => 1, 'amenity_type_id' => 1, 'name' => '{"en":"Essentials"}','description' => '{"en":"Towels, bed sheets, soap, toilet paper, and pillows"}', 'image' => 'essentials.png','list_type' => 'room'],
			['id' => 2, 'amenity_type_id' => 1, 'name' => '{"en":"Wifi"}','description' => NULL, 'image' => 'wifi.png','list_type' => 'hotel'],
			['id' => 3, 'amenity_type_id' => 1, 'name' => '{"en":"TV"}','description' => NULL, 'image' => 'tv.png','list_type' => 'room'],
			['id' => 4, 'amenity_type_id' => 1, 'name' => '{"en":"Heating"}','description' => NULL, 'image' => 'heating.png','list_type' => 'hotel'],
			['id' => 5, 'amenity_type_id' => 1, 'name' => '{"en":"Air conditioning"}','description' => NULL, 'image' => 'air.png','list_type' => 'room'],
			['id' => 6, 'amenity_type_id' => 1, 'name' => '{"en":"Iron"}','description' => NULL, 'image' => 'iron.png','list_type' => 'hotel'],
			['id' => 7, 'amenity_type_id' => 1, 'name' => '{"en":"Shampoo"}','description' => NULL, 'image' => 'shampoo.png','list_type' => 'room'],
			['id' => 8, 'amenity_type_id' => 1, 'name' => '{"en":"Hairdryer"}','description' => NULL, 'image' => 'hairdryer.png','list_type' => 'hotel'],
			['id' => 9, 'amenity_type_id' => 1, 'name' => '{"en":"Desk/workspace"}','description' => NULL, 'image' => 'desk.png','list_type' => 'room'],
			['id' => 10, 'amenity_type_id' => 1, 'name' => '{"en":"Wardrobe/drawers"}','description' => NULL, 'image' => 'wardrobe.png','list_type' => 'hotel'],
			['id' => 11, 'amenity_type_id' => 2, 'name' => '{"en":"Breakfast, coffee, tea"}','description' => NULL, 'image' => 'breakfast.png','list_type' => 'room'],
			['id' => 12, 'amenity_type_id' => 2, 'name' => '{"en":"Private entrance"}','description' => NULL, 'image' => 'private_entrance.png','list_type' => 'hotel'],
			['id' => 13, 'amenity_type_id' => 2, 'name' => '{"en":"Fireplace"}','description' => NULL, 'image' => 'fireplace.png','list_type' => 'room'],
			['id' => 14, 'amenity_type_id' => 3, 'name' => '{"en":"Smoke detector"}','description' => '{"en":"Check your local laws, which may require a working smoke detector in every room."}', 'image' => 'smoke_detector.png','list_type' => 'hotel'],
			['id' => 15, 'amenity_type_id' => 3, 'name' => '{"en":"Carbon monoxide detector"}','description' => '{"en":"Check your local laws, which may require a working carbon monoxide detector in every room."}', 'image' => 'carbon_monoxide_detector.png','list_type' => 'room'],
			['id' => 16, 'amenity_type_id' => 3, 'name' => '{"en":"First aid kit"}','description' => NULL, 'image' => 'first_aid_kit.png','list_type' => 'hotel'],
			['id' => 17, 'amenity_type_id' => 3, 'name' => '{"en":"Fire extinguisher"}','description' => NULL, 'image' => 'fire_extinguisher.png','list_type' => 'room'],
			['id' => 18, 'amenity_type_id' => 3, 'name' => '{"en":"Lock on bedroom door"}','description' => '{"en":"Private room can be locked for safety and privacy"}', 'image' => 'lock_bedroom_door.png','list_type' => 'hotel'],
			['id' => 19, 'amenity_type_id' => 4, 'name' => '{"en":"Lock on bedroom door"}','description' => '{"en":"Private room can be locked for safety and privacy"}', 'image' => 'lock_bedroom_door.png','list_type' => 'room'],
			['id' => 20, 'amenity_type_id' => 5, 'name' => '{"en":"Lock on bedroom door"}','description' => '{"en":"Private room can be locked for safety and privacy"}', 'image' => 'lock_bedroom_door.png','list_type' => 'hotel'],
			['id' => 21, 'amenity_type_id' => 6, 'name' => '{"en":"Lock on bedroom door"}','description' => '{"en":"Private room can be locked for safety and privacy"}', 'image' => 'lock_bedroom_door.png','list_type' => 'room'],
			['id' => 22, 'amenity_type_id' => 7, 'name' => '{"en":"Lock on bedroom door"}','description' => '{"en":"Private room can be locked for safety and privacy"}', 'image' => 'lock_bedroom_door.png','list_type' => 'hotel'],
			['id' => 23, 'amenity_type_id' => 8, 'name' => '{"en":"Lock on bedroom door"}','description' => '{"en":"Private room can be locked for safety and privacy"}', 'image' => 'lock_bedroom_door.png','list_type' => 'room'],
		]);
    }
}
