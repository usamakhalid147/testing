<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SocialMediaLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('social_media_links')->delete();

		DB::table('social_media_links')->insert([
			['name' => 'facebook', 'value' => 'https://www.facebook.com/Cron24-117062946355093'],
			['name' => 'instagram', 'value' => 'https://www.instagram.com/cron24technologies'],
			['name' => 'twitter', 'value' => 'https://twitter.com/Cron24Tech'],
			['name' => 'linkedin', 'value' => 'https://www.linkedin.com/company/cron24-technologies'],
			['name' => 'pinterest', 'value' => 'https://www.pinterest.com/cron24Technologies'],
			['name' => 'youtube', 'value' => 'https://www.youtube.com/channel/UC9yYjwf0DPF8S5PLyO13g6w'],
			['name' => 'skype', 'value' => ''],
		]);
    }
}
