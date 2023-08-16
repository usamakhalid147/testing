<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('currencies')->delete();

		$current_date = date('Y-m-d H:i:s');

		DB::table('currencies')->insert([
			['name' => 'US Dollar','code' => 'USD','symbol' => '$','rate' => '1.000','status' => '1','created_at' => $current_date , 'updated_at' => $current_date],
			['name' => 'Australian Dollar','code' => 'AUD','symbol' => 'A$','rate' => '1.575','status' => '1','created_at' => $current_date , 'updated_at' => $current_date],
			['name' => 'Brazilian Real','code' => 'BRL','symbol' => 'R$','rate' => '5.107','status' => '1','created_at' => $current_date , 'updated_at' => $current_date],
			['name' => 'Canadian Dollar','code' => 'CAD','symbol' => 'C$','rate' => '1.395','status' => '1','created_at' => $current_date , 'updated_at' => $current_date],
			['name' => 'Danish Krone','code' => 'DKK','symbol' => 'kr','rate' => '6.827','status' => '1','created_at' => $current_date , 'updated_at' => $current_date],
			['name' => 'Europe','code' => 'EUR','symbol' => '&#x20AC;','rate' => '0.914','status' => '1','created_at' => $current_date , 'updated_at' => $current_date],
			['name' => 'Hong Kong Dollar','code' => 'HKD','symbol' => 'HK$','rate' => '7.753','status' => '1','created_at' => $current_date , 'updated_at' => $current_date],
			['name' => 'Hungarian Forint','code' => 'HUF','symbol' => 'Ft','rate' => '323.560','status' => '1','created_at' => $current_date , 'updated_at' => $current_date],
			['name' => 'India','code' => 'INR','symbol' => '&#x20B9;','rate' => '76.177','status' => '1','created_at' => $current_date , 'updated_at' => $current_date],
			['name' => 'Israeli New Sheqel','code' => 'ILS','symbol' => '&#x20AA;','rate' => '3.578','status' => '1','created_at' => $current_date , 'updated_at' => $current_date],
			['name' => 'Japanese Yen','code' => 'JPY','symbol' => '&#xA5;','rate' => '108.364','status' => '1','created_at' => $current_date , 'updated_at' => $current_date],
			['name' => 'South Korean Won','code' => 'KRW','symbol' => '&#8361;','rate' => '1182.69', 'status' => '1', 'created_at' => $current_date , 'updated_at' => $current_date],
			['name' => 'Malaysian Ringgit','code' => 'MYR','symbol' => 'RM','rate' => '4.310','status' => '1','created_at' => $current_date , 'updated_at' => $current_date],
			['name' => 'Mexican Peso','code' => 'MXN','symbol' => 'Mex$','rate' => '23.352','status' => '1','created_at' => $current_date , 'updated_at' => $current_date],
			['name' => 'New Taiwan Dollar','code' => 'TWD','symbol' => 'NT$','rate' => '30.041','status' => '1','created_at' => $current_date , 'updated_at' => $current_date],
			['name' => 'Philippine Peso','code' => 'PHP','symbol' => '&#x20B1;','rate' => '50.640','status' => '1','created_at' => $current_date , 'updated_at' => $current_date],
			['name' => 'Polish Zloty','code' => 'PLN','symbol' => 'z&#x142;','rate' => '4.163','status' => '1','created_at' => $current_date , 'updated_at' => $current_date],
			['name' => 'Pound Sterling','code' => 'GBP','symbol' => '&#xA3;','rate' => '0.803','status' => '1','created_at' => $current_date , 'updated_at' => $current_date],
			['name' => 'Russian Ruble','code' => 'RUB','symbol' => 'RUB','rate' => '73.791','status' => '1','created_at' => $current_date , 'updated_at' => $current_date],
			['name' => 'Singapore','code' => 'SGD','symbol' => 'S$','rate' => '1.413','status' => '1','created_at' => $current_date , 'updated_at' => $current_date],
			['name' => 'South African Rand','code' => 'ZAR','symbol' => 'R','rate' => '18.058','status' => '1','created_at' => $current_date , 'updated_at' => $current_date],
			['name' => 'Swedish Krona','code' => 'SEK','symbol' => 'kr','rate' => '9.942','status' => '1','created_at' => $current_date , 'updated_at' => $current_date],
			['name' => 'Swiss Franc','code' => 'CHF','symbol' => 'CHf','rate' => '0.966','status' => '1','created_at' => $current_date , 'updated_at' => $current_date],
			['name' => 'Norwegian Krone','code' => 'NOK','symbol' => 'kr','rate' => '10.203','status' => '1','created_at' => $current_date , 'updated_at' => $current_date],
			['name' => 'New Zealand Dollar','code' => 'NZD','symbol' => '$','rate' => '1.644','status' => '1','created_at' => $current_date , 'updated_at' => $current_date],
			['name' => 'Thai Baht','code' => 'THB','symbol' => '&#xE3F;','rate' => '32.690','status' => '1','created_at' => $current_date , 'updated_at' => $current_date],
			['name' => 'Vietnam Dong','code' => 'VND','symbol' => '₫','rate' => '23525.000','status' => '1','created_at' => $current_date , 'updated_at' => $current_date],
		]);
    }
}
