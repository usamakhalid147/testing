<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GlobalSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('global_settings')->truncate();
        
        DB::table('global_settings')->insert([
            ['name' => 'site_name', 'value' => 'Duhiviet'],
            ['name' => 'site_url', 'value' => ''],
            ['name' => 'version', 'value' => '1.0'],
            ['name' => 'app_version', 'value' => '1.0.0'],
            ['name' => 'force_update', 'value' => '0'],
            ['name' => 'play_store', 'value' => 'https://play.google.com/store/apps/details?id=com.cron24.Hyrahotels'],
            ['name' => 'app_store', 'value' => 'https://play.google.com/store/apps/details?id=com.cron24.Hyrahotels'],
            ['name' => 'starting_year', 'value' => date('Y')],
            ['name' => 'admin_url', 'value' => 'admin'],
            ['name' => 'host_url', 'value' => 'hotelier'],
            ['name' => 'is_locale_based', 'value' => '0'],
            ['name' => 'timezone', 'value' => 'Asia/Kolkata'],
            ['name' => 'upload_driver', 'value' => '0'],
            ['name' => 'support_number', 'value' => '9876543210'],
            ['name' => 'support_email', 'value' => 'support@cron24.com'],
            ['name' => 'default_currency', 'value' => 'USD'],
            ['name' => 'default_language', 'value' => 'en'],
            ['name' => 'date_format', 'value' => '1'],
            ['name' => 'min_price', 'value' => '10'],
            ['name' => 'max_price', 'value' => '1000'],
            ['name' => 'logo', 'value' => 'logo.png'],
            ['name' => 'logo_driver', 'value' => '0'],
            ['name' => 'favicon', 'value' => 'favicon.png'],
            ['name' => 'favicon_driver', 'value' => '0'],
            ['name' => 'head_code', 'value' => ''],
            ['name' => 'featured_cities_text', 'value' => 'Upto 30% OFF on Luxury Experiences'],
            ['name' => 'foot_code', 'value' => ''],
            ['name' => 'user_inactive_days', 'value' => '0'],
            ['name' => 'android_app_maintenance_mode', 'value' => '0'],
            ['name' => 'ios_app_maintenance_mode', 'value' => '0'],
            ['name' => 'maintenance_mode_secret', 'value' => ''],
            ['name' => 'default_user_status', 'value' => 'active'],
            ['name' => 'default_listing_status', 'value' => 'approved'],
            ['name' => 'backup_period', 'value' => 'never'],
            ['name' => 'hotel_admin_commission', 'value' => '10'],
            ['name' => 'auto_payout', 'value' => '0'],
            ['name' => 'host_can_add_coupon', 'value' => '1'],
            ['name' => 'referral_enabled', 'value' => '1'],
            ['name' => 'copyright_link', 'value' => 'https://www.cron24.com/airbnb-clone'],
            ['name' => 'copyright_text', 'value' => 'Circle 2022. All Rights Reserved'],
            ['name' => 'max_review_days', 'value' => '14'],
            ['name' => 'max_guest_dispute_days', 'value' => '7'],
            ['name' => 'max_host_dispute_days', 'value' => '14'],
            ['name' => 'font_script_url', 'value' => 'https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Roboto:wght@300;400;500;700&display=swap'],
            ['name' => 'font_family', 'value' => '\'Roboto\', sans-serif'],
        ]);
    }
}
