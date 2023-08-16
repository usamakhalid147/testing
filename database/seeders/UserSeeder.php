<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		Schema::disableForeignKeyConstraints();
		DB::table('user_informations')->truncate();
		DB::table('user_verifications')->truncate();
		DB::table('user_saved_cards')->truncate();
		DB::table('payout_method_details')->truncate();
		DB::table('payout_methods')->truncate();
		DB::table('users')->truncate();
		Schema::enableForeignKeyConstraints();

		\App\Models\User::insert([
			['id' => '10001','username' => 'peter10001','user_type' => 'host','first_name' => 'Peter','last_name' => 'Pants','email' => 'peter@cron24.com','password' => bcrypt('12345678'), 'google_id' => '103556690527948527469','facebook_id' => NULL,'apple_id' => NULL,'country_code' => 'US','phone_code' => '1','phone_number' => '9876543210','timezone' => 'UTC','user_language' => 'en','user_currency' => 'USD','src' => 'user_10001.jpg', 'status' => 'active','verification_status' => 'no','remember_token' => NULL,'last_active_at' => date('Y-m-d'),'created_at' => '2020-05-07 09:06:02','updated_at' => '2020-07-14 09:26:27'],
			['id' => '10002','username' => 'paige10002','user_type' => 'user','first_name' => 'Paige','last_name' => 'Turner','email' => 'paigeturner@cron24.com','password' => bcrypt('12345678'), 'google_id' => NULL,'facebook_id' => NULL,'apple_id' => NULL,'country_code' => 'US','phone_code' => '1','phone_number' => '9876543211','timezone' => 'UTC','user_language' => 'en','user_currency' => NULL,'src' => 'user_10002.jpg', 'status' => 'active','verification_status' => 'no','remember_token' => NULL,'last_active_at' => date('Y-m-d'),'created_at' => '2020-05-07 09:06:02','updated_at' => '2020-07-10 09:58:54'],
			['id' => '10003','username' => 'paul10003','user_type' => 'host','first_name' => 'Paul','last_name' => 'Molive','email' => 'paul.molive@cron24.com','password' => bcrypt('12345678'), 'google_id' => NULL,'facebook_id' => NULL,'apple_id' => NULL,'country_code' => 'US','phone_code' => '1','phone_number' => '9876543212','timezone' => 'UTC','user_language' => 'en','user_currency' => NULL,'src' => 'user_10003.jpg', 'status' => 'active','verification_status' => 'no','remember_token' => NULL,'last_active_at' => date('Y-m-d'),'created_at' => '2020-05-07 09:06:02','updated_at' => '2020-07-10 09:26:00'],
			['id' => '10004','username' => 'mary10004','user_type' => 'host','first_name' => 'Mary','last_name' => 'Goround','email' => 'marygoround@cron24.com','password' => bcrypt('12345678'), 'google_id' => NULL,'facebook_id' => NULL,'apple_id' => NULL,'country_code' => 'US','phone_code' => '1','phone_number' => '9876543213','timezone' => 'UTC','user_language' => NULL,'user_currency' => NULL,'src' => 'user_10004.jpg', 'status' => 'active','verification_status' => 'no','remember_token' => NULL,'last_active_at' => date('Y-m-d'),'created_at' => '2020-05-07 09:06:02','updated_at' => '2020-05-07 09:06:02'],
			['id' => '10005','username' => 'cory10005','user_type' => 'host','first_name' => 'Cory','last_name' => 'Ander','email' => 'andercory@cron24.com','password' => bcrypt('12345678'), 'google_id' => NULL,'facebook_id' => NULL,'apple_id' => NULL,'country_code' => 'US','phone_code' => '1','phone_number' => '9876543214','timezone' => 'UTC','user_language' => NULL,'user_currency' => NULL,'src' => 'user_10005.jpg', 'status' => 'active','verification_status' => 'no','remember_token' => NULL,'last_active_at' => date('Y-m-d'),'created_at' => '2020-05-07 09:06:02','updated_at' => '2020-05-07 09:06:02'],
			['id' => '10006','username' => 'ben10006','user_type' => 'user','first_name' => 'Ben','last_name' => 'Effit','email' => 'ben@cron24.com','password' => bcrypt('12345678'), 'google_id' => NULL,'facebook_id' => NULL,'apple_id' => NULL,'country_code' => 'US','phone_code' => '1','phone_number' => '9876543215','timezone' => 'UTC','user_language' => NULL,'user_currency' => NULL,'src' => 'user_10006.jpg', 'status' => 'active','verification_status' => 'no','remember_token' => NULL,'last_active_at' => date('Y-m-d'),'created_at' => '2020-05-07 09:06:02','updated_at' => '2020-05-07 09:06:02'],
			['id' => '10007','username' => 'sara10007','user_type' => 'user','first_name' => 'Sara','last_name' => 'Bellum','email' => 'sara@cron24.com','password' => bcrypt('12345678'), 'google_id' => NULL,'facebook_id' => NULL,'apple_id' => NULL,'country_code' => 'US','phone_code' => '1','phone_number' => '9876543216','timezone' => 'UTC','user_language' => NULL,'user_currency' => NULL,'src' => 'user_10007.jpg', 'status' => 'active','verification_status' => 'no','remember_token' => NULL,'last_active_at' => date('Y-m-d'),'created_at' => '2020-05-07 09:06:02','updated_at' => '2020-05-07 09:06:02'],
			['id' => '10008','username' => 'marry10008','user_type' => 'user','first_name' => 'Marry','last_name' => 'Ette','email' => 'marry@cron24.com','password' => bcrypt('12345678'), 'google_id' => NULL,'facebook_id' => NULL,'apple_id' => NULL,'country_code' => 'US','phone_code' => '1','phone_number' => '9876543217','timezone' => 'UTC','user_language' => NULL,'user_currency' => NULL,'src' => 'user_10008.jpg', 'status' => 'active','verification_status' => 'no','remember_token' => NULL,'last_active_at' => date('Y-m-d'),'created_at' => '2020-05-07 09:06:02','updated_at' => '2020-05-07 09:06:02'],
			['id' => '10009','username' => 'helan10009','user_type' => 'user','first_name' => 'Helen','last_name' => 'Highwater','email' => 'helan@cron24.com','password' => bcrypt('12345678'), 'google_id' => NULL,'facebook_id' => NULL,'apple_id' => NULL,'country_code' => 'US','phone_code' => '1','phone_number' => '9876543218','timezone' => 'UTC','user_language' => NULL,'user_currency' => NULL,'src' => 'user_10009.jpg', 'status' => 'active','verification_status' => 'no','remember_token' => NULL,'last_active_at' => date('Y-m-d'),'created_at' => '2020-05-07 09:06:02','updated_at' => '2020-05-07 09:06:02'],
			['id' => '10010','username' => 'mick10010','user_type' => 'user','first_name' => 'Mick','last_name' => 'Donalds','email' => 'mick@cron24.com','password' => bcrypt('12345678'), 'google_id' => NULL,'facebook_id' => NULL,'apple_id' => NULL,'country_code' => 'US','phone_code' => '1','phone_number' => '9876543219','timezone' => 'UTC','user_language' => NULL,'user_currency' => NULL,'src' => 'user_10010.jpg', 'status' => 'active','verification_status' => 'no','remember_token' => NULL,'last_active_at' => date('Y-m-d'),'created_at' => '2020-05-07 09:06:02','updated_at' => '2020-05-07 09:06:02'],
		]);

		DB::table('user_informations')->insert([
			['user_id' => '10001', 'dob' => '1990-03-18', 'gender' => 'Male', 'about' => 'Life is a play so enchanting!  Peter is a songwriter/ musician/ carpenter. Parker is a painter/ trash fashion designer/ musician. We\'re friendly and easygoing. Parker and I met in Italy and we love to travel to visit friends and family. We also love to see beautiful views, historic places, great museums and different cultures. We really love being outdoors in nature. Nature is divine!. Please come check it out!','location' => 'San Francisco,US','work' => '','languages' => 'ca,en,fa'],
			['user_id' => '10002', 'dob' => '1996-08-25', 'gender' => 'Female', 'about' => 'I love SF and I enjoy talking about our great city with our events. My girlfriend (Graciela) and I both enjoy meeting people from all over the world.','location' => 'San Francisco, California, United States','work' => NULL,'languages' => NULL],
			['user_id' => '10003', 'dob' => '1992-04-18', 'gender' => 'Male', 'about' => 'My fiancé and I are both born and raised Millbillys (her family goes back 3 generations)~ we\'ve got to little pups and 2 house kitty\'s all rescue (hugs animal lovers). We enjoy all the hiking trails right outside our front door and well as dining (outdoors for now) at sone of our favorite restaurants in town (a 10 min walk). We love sharing our space for others to enjoy. Mill Valley is truly a magical place.','location' => NULL,'work' => NULL,'languages' => NULL],
			['user_id' => '10004', 'dob' => '1995-07-14', 'gender' => 'Female', 'about' => 'I am an avid traveler, with a passion for creating beauty, be it in designing a room, restyling a space, or redecorating my homes, which I do frequently. When planning travel, (and I have been staying in vacation rentals for twenty years), I look carefully at the details. Is there a window over the sink? Does the bedroom get morning light? Did they photograph the bathroom with the toilet seat up? (I know, but that\'s an important detail in hospitality). I have owned and managed two luxury B&B\'s in Jackson Hole, Wyoming. Back in my home state of California, I am launching a new venture that is a dream of mine, and organic food farm. This will be a slow launch, but I\'m excited for the road ahead. I have lived in Florence, Italy, my favorite place on earth, and attended culinary school there at a James Beard Academy. My kitchens reflect my other passion for hosting guests and serving good food. My priorities in life have changed through the years, and with three grown children out traveling the world','location' => NULL,'work' => NULL,'languages' => NULL],
			['user_id' => '10005', 'dob' => '1990-01-03', 'gender' => 'Male', 'about' => NULL,'location' => NULL,'work' => NULL,'languages' => NULL],
			['user_id' => '10006', 'dob' => '1990-03-18', 'gender' => 'Male', 'about' => 'Life is a play so enchanting!  Peter is a songwriter/ musician/ carpenter. Parker is a painter/ trash fashion designer/ musician. We\'re friendly and easygoing. Parker and I met in Italy and we love to travel to visit friends and family. We also love to see beautiful views, historic places, great museums and different cultures. We really love being outdoors in nature. Nature is divine!. Please come check it out!','location' => 'San Francisco,US','work' => '','languages' => 'ca,en,fa'],
			['user_id' => '10007', 'dob' => '1996-08-25', 'gender' => 'Female', 'about' => 'I love SF and I enjoy talking about our great city with our events. My girlfriend (Graciela) and I both enjoy meeting people from all over the world.','location' => 'San Francisco, California, United States','work' => NULL,'languages' => NULL],
			['user_id' => '10008', 'dob' => '1992-04-18', 'gender' => 'Female', 'about' => 'My fiancé and I are both born and raised Millbillys (her family goes back 3 generations)~ we\'ve got to little pups and 2 house kitty\'s all rescue (hugs animal lovers). We enjoy all the hiking trails right outside our front door and well as dining (outdoors for now) at sone of our favorite restaurants in town (a 10 min walk). We love sharing our space for others to enjoy. Mill Valley is truly a magical place.','location' => NULL,'work' => NULL,'languages' => NULL],
			['user_id' => '10009', 'dob' => '1995-07-14', 'gender' => 'Female', 'about' => 'I am an avid traveler, with a passion for creating beauty, be it in designing a room, restyling a space, or redecorating my homes, which I do frequently. When planning travel, (and I have been staying in vacation rentals for twenty years), I look carefully at the details. Is there a window over the sink? Does the bedroom get morning light? Did they photograph the bathroom with the toilet seat up? (I know, but that\'s an important detail in hospitality). I have owned and managed two luxury B&B\'s in Jackson Hole, Wyoming. Back in my home state of California, I am launching a new venture that is a dream of mine, and organic food farm. This will be a slow launch, but I\'m excited for the road ahead. I have lived in Florence, Italy, my favorite place on earth, and attended culinary school there at a James Beard Academy. My kitchens reflect my other passion for hosting guests and serving good food. My priorities in life have changed through the years, and with three grown children out traveling the world','location' => NULL,'work' => NULL,'languages' => NULL],
			['user_id' => '10010', 'dob' => '1990-01-03', 'gender' => 'Male', 'about' => NULL,'location' => NULL,'work' => NULL,'languages' => NULL],
		]);

		DB::table('user_verifications')->insert([
			array('user_id' => '10001', 'email' => '1', 'facebook' => '0', 'google' => '1', 'phone_number' => '1'),
			array('user_id' => '10002', 'email' => '1', 'facebook' => '0', 'google' => '0', 'phone_number' => '1'),
			array('user_id' => '10003', 'email' => '1', 'facebook' => '0', 'google' => '0', 'phone_number' => '1'),
			array('user_id' => '10004', 'email' => '1', 'facebook' => '0', 'google' => '0', 'phone_number' => '0'),
			array('user_id' => '10005', 'email' => '1', 'facebook' => '0', 'google' => '0', 'phone_number' => '1'),
			array('user_id' => '10006', 'email' => '1', 'facebook' => '0', 'google' => '1', 'phone_number' => '1'),
			array('user_id' => '10007', 'email' => '1', 'facebook' => '0', 'google' => '0', 'phone_number' => '1'),
			array('user_id' => '10008', 'email' => '1', 'facebook' => '0', 'google' => '0', 'phone_number' => '1'),
			array('user_id' => '10009', 'email' => '1', 'facebook' => '0', 'google' => '0', 'phone_number' => '0'),
			array('user_id' => '10010', 'email' => '1', 'facebook' => '0', 'google' => '0', 'phone_number' => '1'),
		]);

		DB::table('user_saved_cards')->insert([
  			array('id' => '1','user_id' => '10001','customer_id' => 'cus_KjV2RghuBNjz15','payment_method' => 'pm_1K422dAhMTNAsHCV3z7FxRtH','brand' => 'Visa','last4' => '0077','exp_month' => '2','exp_year' => '2025','created_at' => '2021-12-07 17:08:16','updated_at' => '2021-12-07 17:08:19'),
  			array('id' => '2','user_id' => '10002','customer_id' => 'cus_KjUzLSvgPWOxdT','payment_method' => 'pm_1K41zxAhMTNAsHCVdtYbms61','brand' => 'Visa','last4' => '0077','exp_month' => '2','exp_year' => '2025','created_at' => '2021-12-07 17:11:03','updated_at' => '2021-12-07 17:11:05'),
		]);

		DB::table('payout_methods')->insert([
			['id' => '1','user_id' => '10001','is_default' => '1','method_type' => 'stripe','currency_code' => 'USD','payout_id' => 'acct_1HTqcJIaHm1cONGG','created_at' => '2020-07-10 09:25:27','updated_at' => '2020-07-10 09:25:27'],
			['id' => '2','user_id' => '10003','is_default' => '1','method_type' => 'paypal','currency_code' => 'USD','payout_id' => 'paul.molive@cron24.com','created_at' => '2020-07-10 09:28:28','updated_at' => '2020-07-10 09:28:28'],
		]);

		DB::table('payout_method_details')->insert([
			['id' => '1','payout_method_id' => '1','address1' => '3 E. Pine Street','address2' => '','city' => 'Syosset','state' => 'NY','postal_code' => '11791','country_code' => 'US','payout_id' => 'acct_1HTqcJIaHm1cONGG','currency_code' => 'USD','routing_number' => '110000000','account_number' => '000123456789','ssn_last_4' => '0000','holder_name' => 'Peter Pants','document_id' => 'file_1H3ITxCbN8wfTd4g4kJoutUf','document_path' => 'payout_legal1594373114.jpg','additional_document_id' => 'file_1H3ITzCbN8wfTd4gQ2AZGmWS','additional_document_path' => 'payout_additional1594373114.jpg','phone_number' => '8754727065','address_kanji' => '[]','bank_name' => '','bank_location' => '','branch_name' => '','branch_code' => '','created_at' => '2020-07-10 09:25:27','updated_at' => '2020-07-10 09:25:27'],
			['id' => '2','payout_method_id' => '2','address1' => '3 Doris St','address2' => '','city' => 'North Sydney','state' => 'NSW','postal_code' => '2060','country_code' => 'AU','payout_id' => 'aul.molive@cron24.com','currency_code' => 'USD','routing_number' => '','account_number' => '','ssn_last_4' => '','holder_name' => '','document_id' => NULL,'document_path' => NULL,'additional_document_id' => NULL,'additional_document_path' => NULL,'phone_number' => '','address_kanji' => '[]','bank_name' => '','bank_location' => '','branch_name' => '','branch_code' => '','created_at' => '2020-07-10 09:28:28','updated_at' => '2020-07-10 09:28:28'],
		]);

		\App\Models\User::where('user_type','host')->get()->each(function($user) {
			if ($user->user_type == 'host') {
				$config = config('entrust_seeder.role_structure');
				$userRoles = config('entrust_seeder.user_roles');
				$mapPermission = collect(config('entrust_seeder.permissions_map'));

				$modules = $config['host'];
				$key = 'host';
            // Create a new role
				$role = \App\Models\Role::create([
					'name' => $key.'_'.$user->first_name,
					'user_type' => $key,
					'user_id' => $user->id,
					'display_name' => ucwords(str_replace('_', ' ', $key)),
					'description' => ucwords(str_replace('_', ' ', $key))
				]);

				$permissions = \App\Models\Permission::where('user_type',$user->user_type)->get();

            // Attach all permissions to the role
				$role->permissions()->sync($permissions);
				$user->attachRole($role);
			}
		});

		\App\Models\User::where('user_type','host')->get()->each(function($user) {
			if ($user->user_type == 'host') {
				$company = new \App\Models\Company;
				$company->user_id = $user->id;
				$company->save();
			}
		});
    }
}
