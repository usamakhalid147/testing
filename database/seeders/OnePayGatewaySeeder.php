<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OnePayGatewaySeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('credentials')->insert([
			['name' => 'is_enabled','value' => '1','site' => 'OnePay'],
			['name' => 'paymode','value' => 'sandbox','site' => 'OnePay'],
			['name' => 'payment_currency','value' => 'VND','site' => 'OnePay'],
			['name' => 'access_code','value' => '6BEB2546','site' => 'OnePay'],
			['name' => 'merchant','value' => 'TESTONEPAY','site' => 'OnePay'],
			['name' => 'hash_key','value' => '6D0870CDE5F24F34F3915FB0045120DB','site' => 'OnePay'],
		]);
    }
}
