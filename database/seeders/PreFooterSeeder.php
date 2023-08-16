<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PreFooterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('pre_footers')->delete();

        $currentDateTime = date('Y-m-d H:i:s');

		DB::table('pre_footers')->insert([
			['id' => 1, 'title' => '{"en":"Come to the Tropical paraise 2022"}', 'description' => '{"en":"Lorem Ipsum is simply dummy text of the printing and typesetting industry."}', 'image' => 'pre_footer_1634042555.png', 'upload_driver' => '0', 'status' => '1', 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
			['id' => 2, 'title' => '{"en":"Come to the Tropical paraise 2022"}', 'description' => '{"en":"Lorem Ipsum is simply dummy text of the printing and typesetting industry."}', 'image' => 'pre_footer_1634042616.png', 'upload_driver' => '0', 'status' => '1', 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
		]);
    }
}
