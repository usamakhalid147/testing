<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BedTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('bed_types')->delete();

		DB::table('bed_types')->insert([
			['id' => 1, 'name' => '{"en":"Double"}', 'image' => 'double.png', 'status' => 1],
			['id' => 2, 'name' => '{"en":"Queen"}', 'image' => 'queen.png', 'status' => 1],
			['id' => 3, 'name' => '{"en":"Single"}', 'image' => 'single.png', 'status' => 1],
			['id' => 4, 'name' => '{"en":"Sofa bed"}', 'image' => 'sofa_bed.png', 'status' => 1],
			['id' => 5, 'name' => '{"en":"King"}', 'image' => 'king_bed.png', 'status' => 1],
			['id' => 6, 'name' => '{"en":"Small double"}', 'image' => 'small_double_bed.png', 'status' => 1],
			['id' => 7, 'name' => '{"en":"Sofa"}', 'image' => 'couch.png','status' => 1],
			['id' => 8, 'name' => '{"en":"Bunk bed"}', 'image' => 'bunk_bed.png','status' => 1],
			['id' => 9, 'name' => '{"en":"Floor matress"}', 'image' => 'floor_mattress.png','status' => 1],
			['id' => 10, 'name' => '{"en":"Airbed"}', 'image' => 'air_mattress.png','status' => 1],
			['id' => 11, 'name' => '{"en":"Cot"}', 'image' => 'crib.png', 'status' => 1],
			['id' => 12, 'name' => '{"en":"Toddler bed"}', 'image' => 'toddler_bed.png', 'status' => 1],
			['id' => 13, 'name' => '{"en":"Hammock"}', 'image' => 'hammock.png', 'status' => 1],
			['id' => 14, 'name' => '{"en":"Water bed"}', 'image' => 'water_bed.png', 'status' => 1],
		]);
    }
}
