<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('discount_banners')->delete();
        DB::table('login_sliders')->delete();
        DB::table('sliders')->delete();

		$currentDateTime = date('Y-m-d H:i:s');

		DB::table('discount_banners')->insert([
			['id' => 1, 'image' => 'hotel_offer1.jpg','order_id' => '1','upload_driver' => '0','created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => 2, 'image' => 'hotel_offer2.jpg','order_id' => '2','upload_driver' => '0','created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
		]);

		DB::table('login_sliders')->insert([
			['id' => 1, 'type' => 'admin', 'image' => 'admin_slider_1.jpg','order_id' => '1','upload_driver' => '0','created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => 2, 'type' => 'admin', 'image' => 'admin_slider_2.jpg','order_id' => '2','upload_driver' => '0','created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => 3, 'type' => 'admin', 'image' => 'admin_slider_3.jpg','order_id' => '3','upload_driver' => '0','created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
		]);

		DB::table('sliders')->insert([
			['id' => 1, 'title' => '{"en":"Find deals on hotels, homes and much more..."}', 'image' => 'slider_1.jpg','order_id' => '1','upload_driver' => '0', 'status' => 1,'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => 2, 'title' => '{"en":"Machu Picchu, Peru"}', 'image' => 'slider_2.jpg','order_id' => '2','upload_driver' => '0', 'status' => 1,'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => 3, 'title' => '{"en":"Great Barrier Reef, Australia"}', 'image' => 'slider_3.jpg','order_id' => '3','upload_driver' => '0', 'status' => 1,'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => 4, 'title' => '{"en":"Rio de janeiro, Brazil"}', 'image' => 'slider_4.jpg','order_id' => '4','upload_driver' => '0', 'status' => 1,'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
		]);
    }
}
