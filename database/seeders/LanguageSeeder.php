<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('languages')->delete();

		DB::table('languages')->insert([
			['code' => 'ar', 'name' => 'العربية'],
			['code' => 'ca', 'name' => 'Català'],
			['code' => 'da', 'name' => 'Dansk'],
			['code' => 'de', 'name' => 'Deutsche'],
			['code' => 'en', 'name' => 'English'],
			['code' => 'el', 'name' => 'Eλληνικά'],
			['code' => 'es', 'name' => 'Español'],
			['code' => 'fa', 'name' => 'فارسی'],
			['code' => 'fi', 'name' => 'Suomi'],
			['code' => 'fr', 'name' => 'Français'],
			['code' => 'hi', 'name' => 'हिन्दी'],
			['code' => 'hu', 'name' => 'Hungarian'],
			['code' => 'id', 'name' => 'bahasa Indonesia'],
			['code' => 'it', 'name' => 'Italiana'],
			['code' => 'ja', 'name' => '日本語'],
			['code' => 'ko', 'name' => '한국어'],
			['code' => 'ms', 'name' => 'Melayu'],
			['code' => 'nl', 'name' => 'Nederlands'],
			['code' => 'pl', 'name' => 'Polskie'],
			['code' => 'pt', 'name' => 'Português'],
			['code' => 'ru', 'name' => 'русский'],
			['code' => 'ta', 'name' => 'தமிழ்'],
			['code' => 'th', 'name' => 'ภาษาไทย'],
			['code' => 'tr', 'name' => 'Türkçe'],
			['code' => 'zh', 'name' => '中文'],
		]);
    }
}
