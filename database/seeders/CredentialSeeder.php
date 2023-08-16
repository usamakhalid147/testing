<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CredentialSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('credentials')->truncate();

		DB::table('credentials')->insert([
			['name' => 'driver', 'value' => 'smtp','site' => 'EmailConfig'],
			['name' => 'host', 'value' => 'smtp.gmail.com','site' => 'EmailConfig'],
			['name' => 'port', 'value' => '587','site' => 'EmailConfig'],
			['name' => 'from_address', 'value' => 'crontwentyfour@gmail.com','site' => 'EmailConfig'],
			['name' => 'from_name', 'value' => 'HyraHotel','site' => 'EmailConfig'],
			['name' => 'encryption', 'value' => 'tls','site' => 'EmailConfig'],
			['name' => 'username', 'value' => 'crontwentyfour@gmail.com','site' => 'EmailConfig'],
			['name' => 'password', 'value' => 'xznhpohaijnwaiaj','site' => 'EmailConfig'],

			['name' => 'is_enabled', 'value' => '1', 'site' => 'googleMap'],
			['name' => 'map_api_key', 'value' => 'AIzaSyAbnGd00W_OALQTGt9FnMA9NdGqXnjhwcA', 'site' => 'googleMap'],
			['name' => 'map_server_key', 'value' => 'AIzaSyDwcmdYrXzx54KAM82VcJJC-RP5P8NQN8Y', 'site' => 'googleMap'],

			['name' => 'is_enabled', 'value' => '1', 'site' => 'Google'],
			['name' => 'client_id', 'value' => '427222322605-70vks78jfcn1ifp823chl1qdpjmp52j2.apps.googleusercontent.com', 'site' => 'Google'],
			['name' => 'secret_key', 'value' => 'AhrGi4cfJGXea0bg0WxbhdbL', 'site' => 'Google'],

			['name' => 'is_enabled', 'value' => '1', 'site' => 'Facebook'],
			['name' => 'app_id', 'value' => '223130132263996', 'site' => 'Facebook'],
			['name' => 'app_secret', 'value' => 'c83755a456ab82d771f28473a0526fc3', 'site' => 'Facebook'],

			['name' => 'is_enabled', 'value' => '0', 'site' => 'Apple'],
			['name' => 'service_id', 'value' => 'com.cron24.hyrahotels.serviceId', 'site' => 'Apple'],
			['name' => 'team_id', 'value' => 'ZQR8N9BWLP', 'site' => 'Apple'],
			['name' => 'key_id', 'value' => 'JXZ3H4292M', 'site' => 'Apple'],
			['name' => 'key_file', 'value' => 'key.txt', 'site' => 'Apple'],

			['name' => 'is_enabled', 'value' => '1', 'site' => 'Stripe'],
			['name' => 'is_default', 'value' => '1', 'site' => 'Stripe'],
			['name' => 'payment_currency', 'value' => 'USD', 'site' => 'Stripe'],
			['name' => 'api_version', 'value' => '2020-03-02', 'site' => 'Stripe'],
			['name' => 'publish_key', 'value' => 'pk_test_Wdiq3tqelLpWpgGaZTsAqmqc00mAfCO77u', 'site' => 'Stripe'],
			['name' => 'secret_key', 'value' => 'sk_test_fJyM0EGSaiIhyWbS7ewGRJ8900SyDYtN0S', 'site' => 'Stripe'],
			['name' => 'account_type', 'value' => 'express', 'site' => 'Stripe'],

			['name' => 'is_enabled', 'value' => '1', 'site' => 'Paypal'],
			['name' => 'is_default', 'value' => '0', 'site' => 'Paypal'],
			['name' => 'payment_currency', 'value' => 'USD', 'site' => 'Paypal'],
			['name' => 'integration_date', 'value' => '2020-05-07', 'site' => 'Paypal'],
			['name' => 'paymode', 'value' => 'sandbox', 'site' => 'Paypal'],
			['name' => 'client_id', 'value' => 'Aadd2CapsSVqviMjaCXi9lBCg3wBccCM9gwYYVQu36ksZ2ey1Z3Tf0BJPqh-oMBndk73MeUk0wut_d7D', 'site' => 'Paypal'],
			['name' => 'secret_key', 'value' => 'EB4ifk_NGQ1BYnQIOCS7YBTlBixDZPf1WnzWLTtJleCzvT8IERsIqSwEVWWvIF36AcdOi3LB1P4fNCVh', 'site' => 'Paypal'],

			['name' => 'is_enabled', 'value' => '1', 'site' => 'BankTransfer'],
			['name' => 'is_default', 'value' => '0', 'site' => 'BankTransfer'],
			
			['name' => 'cloud_name','value' => 'clouddemoapp','site' => 'Cloudinary'],
			['name' => 'api_key','value' => '479759278144296','site' => 'Cloudinary'],
			['name' => 'api_secret','value' => 'epWtaJvjYFOABOsCaNBnYSq1zIg','site' => 'Cloudinary'],

			['name' => 'is_enabled', 'value' => '0', 'site' => 'Twilio'],
			['name' => 'account_sid', 'value' => 'AC958ea24ae5003383d1e9ac51994cbc69', 'site' => 'Twilio'],
			['name' => 'auth_token', 'value' => '2cc0aa57eb937c660ddfbd938acabbe7', 'site' => 'Twilio'],
			['name' => 'from_number', 'value' => '+12055066615', 'site' => 'Twilio'],

			['name' => 'is_enabled','value' => '0','site' => 'ReCaptcha'],
			['name' => 'version','value' => '2','site' => 'ReCaptcha'],
			['name' => 'site_key','value' => '6LcF1bUaAAAAAIFgygQFz4fDYIrB0LVK8FvfL6AG','site' => 'ReCaptcha'],
			['name' => 'secret_key','value' => '6LcF1bUaAAAAAG4NukEA1De87YO5ziWDzv8q9Wo3','site' => 'ReCaptcha'],

			['name' => 'is_enabled','value' => '0','site' => 'Firebase'],
			['name' => 'api_key', 'value' => 'AIzaSyDODa-JIVd0r-xUPcjMjCdC5K2xTFgiKac', 'site' => 'Firebase'],
			['name' => 'server_key', 'value' => 'AAAAI4yZRSc:APA91bE0bx0Z_m2UWC2csyWzFzP67mvVWYe-n4y94fcLX0Wi6PnNT2kUBYYALCM1EfU-885iptdYEM-qPRjztgP_Zt0QF2zZPoHXfSnzCyeLiAoStv1R3o0p2aKoBEaFFi3yqoPNw-FZ', 'site' => 'Firebase'],
			['name' => 'auth_domain', 'value' => 'hyra-a3439.firebaseapp.com', 'site' => 'Firebase'],
			['name' => 'database_url', 'value' => 'https://hyra-a3439-default-rtdb.firebaseio.com', 'site' => 'Firebase'],
			['name' => 'project_id', 'value' => 'hyra-a3439', 'site' => 'Firebase'],
			['name' => 'storage_bucket', 'value' => 'hyra-a3439.appspot.com', 'site' => 'Firebase'],
			['name' => 'messaging_sender_id', 'value' => '152682710311', 'site' => 'Firebase'],
			['name' => 'app_id', 'value' => '1:152682710311:web:8438a82fbda600ab6e1502', 'site' => 'Firebase'],
			['name' => 'service_account', 'value' => 'hyra-a3439-firebase-adminsdk-vzw36-619a002b29.json', 'site' => 'Firebase'],

			['name' => 'is_enabled','value' => '0','site' => 'Conveythis'],
		]);
    }
}
